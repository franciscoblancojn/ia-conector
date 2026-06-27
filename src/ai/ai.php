<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class IACON_AI
{
    private static function getConfig()
    {
        $IACON_USE_DATA_CONFIG = new IACON_USE_DATA_CONFIG();
        return $IACON_USE_DATA_CONFIG->get();
    }
    private static function request(
        $url,
        $method = "GET",
        $data = null
    ) {
        $jsonResponse = [];

        try {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $timeout = 300000;
            if (defined('IACON_HTTP_TIMEOUT')) {
                $timeout = IACON_HTTP_TIMEOUT;
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, true);
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            if (isset($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            }

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new \RuntimeException('Error en cURL: ' . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $jsonResponse = json_decode($response, true);

            if ($httpCode >= 400) {
                throw new \RuntimeException(
                    'API Error: ' . ($jsonResponse['error']['message'] ?? 'Error desconocido')
                );
            }

            if (isset($jsonResponse['error'])) {
                throw new \RuntimeException(
                    'API Error: ' . $jsonResponse['error']['message']
                );
            }

            return [
                "status" => "ok",
                "message" => "Respuesta Exitosa",
                'data' => $jsonResponse
            ];
        } catch (\Throwable $th) {

            $error = [
                "status" => "error",
                "message" => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    'jsonResponse' => $jsonResponse
                ]
            ];

            FWUSystemLog::add(IACON_KEY, [
                'type' => "IA error",
                'data' => $error
            ]);

            return $error;
        }
    }
    public static function sendPrompt($PROMPT)
    {
        $jsonResponse = [];

        $mock = apply_filters('ia_conector_ai_mock_response', null, $PROMPT);
        if ($mock !== null) {
            do_action('ia_conector_ai_mock_used', $PROMPT, $mock);
            return $mock;
        }

        $PROMPT = apply_filters('ia_conector_ai_prompt', $PROMPT);

        try {
            $CONFIG = self::getConfig();
            $gemini = $CONFIG['gemini'] ?? [];
            $apiKey = $gemini['apikey'];
            $modelo = $gemini['modelo'];
            $url = "https://generativelanguage.googleapis.com/v1/models/{$modelo}:generateContent?key={$apiKey}";

            $url = apply_filters('ia_conector_ai_api_url', $url, $PROMPT, $modelo, $apiKey);

            $data = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $PROMPT]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "maxOutputTokens" => 65536,
                    "temperature" => 0.2
                ]
            ];

            $data = apply_filters('ia_conector_ai_request_data', $data, $PROMPT);

            do_action('ia_conector_ai_before_request', $PROMPT, $data, $url);

            $result = self::request($url, "POST", $data);

            do_action('ia_conector_ai_after_request', $PROMPT, $data, $url, $result);

            if ($result['status'] == 'error') {
                return $result;
            }
            $jsonResponse = $result['data'];
            if (isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
                $data = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
                return [
                    "status" => "ok",
                    "message" => "Respuesta Exitosa",
                    'data' => $data,
                ];
            } else {
                throw new \RuntimeException('Error en cURL');
            }
        } catch (\Throwable $th) {
            $error = [
                "status" => "error",
                "message" => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    'jsonResponse' => $jsonResponse
                ]
            ];
            FWUSystemLog::add(IACON_KEY, [
                'type' => "IA error",
                'data' => $error
            ]);
            return $error;
        }
    }
    public static function getModels()
    {
        $jsonResponse = [];

        try {
            $CONFIG = self::getConfig();
            $gemini = $CONFIG['gemini'] ?? [];
            $apiKey = $gemini['apikey'];

            $url = "https://generativelanguage.googleapis.com/v1/models?key={$apiKey}";

            $result = self::request($url);
            if ($result['status'] == 'error') {
                return $result;
            }
            $jsonResponse = $result['data'];

            $models = [];

            if (!empty($jsonResponse['models']) && is_array($jsonResponse['models'])) {
                foreach ($jsonResponse['models'] as $model) {

                    $methods = $model['supportedGenerationMethods'] ?? [];

                    if (!in_array('generateContent', $methods)) {
                        continue;
                    }

                    $models[] = [
                        'name' => $model['name'],
                        'model' => str_replace('models/', '', $model['name']),
                        'displayName' => $model['displayName'] ?? $model['name'],
                    ];
                }
            }

            return [
                "status" => "ok",
                "message" => "Modelos obtenidos correctamente",
                "data" => $models,
            ];
        } catch (\Throwable $th) {
            $error = [
                "status" => "error",
                "message" => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    'jsonResponse' => $jsonResponse
                ]
            ];

            FWUSystemLog::add(IACON_KEY, [
                'type' => "IA modelos error",
                'data' => $error
            ]);

            return $error;
        }
    }
    public static function parseJson($dataString)
    {
        if (!$dataString) {
            throw new \RuntimeException('Respuesta vacía');
        }

        $dataString = apply_filters('ia_conector_ai_parse_raw', $dataString);

        $dataString = preg_replace('/^```json\s*/i', '', $dataString);
        $dataString = preg_replace('/^```/i', '', $dataString);
        $dataString = preg_replace('/```$/', '', $dataString);

        $dataString = trim($dataString);

        $data = json_decode($dataString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            if (preg_match('/(\{.*\}|\[.*\])/s', $dataString, $matches)) {
                $dataString = $matches[0];
                $data = json_decode($dataString, true);
            }
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'Error al parsear JSON: ' . json_last_error_msg() .
                    ' | String recibido: ' . substr($dataString, 0, 500)
            );
        }

        $data = apply_filters('ia_conector_ai_parsed_data', $data);

        return $data;
    }
}
