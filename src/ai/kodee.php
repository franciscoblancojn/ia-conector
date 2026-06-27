<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class IACON_KODEE
{
    public static function isAvailable()
    {
        return function_exists('is_plugin_active') && is_plugin_active('hostinger-ai-assistant/hostinger-ai-assistant.php');
    }

    private static function getApiToken()
    {
        if (defined('HOSTINGER_AI_ASSISTANT_WP_AI_TOKEN') && file_exists(HOSTINGER_AI_ASSISTANT_WP_AI_TOKEN)) {
            $token = trim(file_get_contents(HOSTINGER_AI_ASSISTANT_WP_AI_TOKEN));
            if (!empty($token)) {
                return $token;
            }
        }

        if (defined('IACON_DIR')) {
            $parts = explode('/', IACON_DIR);
            if (isset($parts[1], $parts[2])) {
                $serverRootPath = '/' . $parts[1] . '/' . $parts[2];
                $tokenPath = $serverRootPath . '/.api_token';
                if (file_exists($tokenPath)) {
                    $token = trim(file_get_contents($tokenPath));
                    if (!empty($token)) {
                        return $token;
                    }
                }
            }
        }

        $paths = [
            ABSPATH . '.api_token',
            dirname(ABSPATH) . '/.api_token',
            dirname(dirname(ABSPATH)) . '/.api_token',
            $_SERVER['DOCUMENT_ROOT'] . '/.api_token',
            dirname($_SERVER['DOCUMENT_ROOT']) . '/.api_token',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $token = trim(file_get_contents($path));
                if (!empty($token)) {
                    return $token;
                }
            }
        }

        return '';
    }

    private static function getDomain()
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    public static function sendPrompt($PROMPT)
    {
        if (!self::isAvailable()) {
            $error = [
                'status' => 'error',
                'message' => 'Hostinger AI Assistant no está activo. Instala y activa el plugin hostinger-ai-assistant.',
            ];
            FWUSystemLog::add(IACON_KEY, [
                'type' => 'kodee_error',
                'data' => $error,
            ]);
            return $error;
        }

        $apiToken = self::getApiToken();
        if (empty($apiToken)) {
            $error = [
                'status' => 'error',
                'message' => 'No se encontró el token de API de Hostinger (.api_token). Asegúrate de estar en un servidor Hostinger.',
            ];
            FWUSystemLog::add(IACON_KEY, [
                'type' => 'kodee_error',
                'data' => $error,
            ]);
            return $error;
        }

        try {
            $url = add_query_arg(
                [
                    'post_type' => 'blog_post',
                    'tone' => 'neutral',
                    'length' => '600-1200',
                    'description' => $PROMPT,
                    'focus_keyword' => '',
                ],
                'https://rest-hosting.hostinger.com/v3/wordpress/plugin/generate-content'
            );

            $response = wp_remote_get($url, [
                'timeout' => 120,
                'headers' => [
                    'X-Hpanel-Order-Token' => $apiToken,
                    'X-Hpanel-Domain' => self::getDomain(),
                ],
            ]);

            if (is_wp_error($response)) {
                throw new \RuntimeException('Error en la petición: ' . $response->get_error_message());
            }

            $httpCode = wp_remote_retrieve_response_code($response);
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if ($httpCode >= 400) {
                $errorMsg = $data['error']['message'] ?? 'Error HTTP ' . $httpCode;
                throw new \RuntimeException($errorMsg);
            }

            if (isset($data['data']) && is_array($data['data'])) {
                $generated = reset($data['data']);
                $content = '';

                if (is_object($generated) && isset($generated->content)) {
                    $content = $generated->content;
                } elseif (is_array($generated) && isset($generated['content'])) {
                    $content = $generated['content'];
                } elseif (is_string($generated)) {
                    $content = $generated;
                } else {
                    $content = wp_json_encode($generated);
                }
                FWUSystemLog::add(IACON_KEY, [
                    'type' => 'kodee_ok',
                    'data' => [
                        "PROMPT" => $PROMPT,
                        "content" => $content,
                    ],
                ]);

                return [
                    'status' => 'ok',
                    'message' => 'Respuesta Exitosa',
                    'data' => $content,
                ];
            }

            throw new \RuntimeException('Formato de respuesta inesperado');
        } catch (\Throwable $th) {
            $error = [
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                ],
            ];

            FWUSystemLog::add(IACON_KEY, [
                'type' => 'kodee_error',
                'data' => $error,
            ]);

            return $error;
        }
    }
}
