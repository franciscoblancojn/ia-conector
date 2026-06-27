---
name: ia-conector-plugin
description: >-
  Use ONLY when working on IA Conector plugin features: AI configuration (Gemini),
  test prompts, or adding new AI providers. Contains plugin-specific validation
  rules, class references, and data flow constraints.
---

# IA Conector Plugin — Reglas Específicas del Plugin

Actíva esta skill cuando trabajes con funcionalidades propias del plugin IA Conector.

---

## Conexiones de IA

### Estructura de Configuración

Cada IA se guarda como una clave dentro de `IACON_CONFIG` (wp_options):

```php
$CONFIG = [
    'gemini' => [
        'apikey'       => '...',
        'modelo'       => 'gemini-2.0-flash',
        'list_modelos' => [
            ['name' => 'models/gemini-2.0-flash', 'model' => 'gemini-2.0-flash', 'displayName' => 'Gemini 2.0 Flash'],
            ...
        ],
    ],
    // 'openai' => [ ... ],  // Futuras IAs
];
```

### Agregar una Nueva IA

1. Crear sección en `src/page/sections/{key}.php`.
2. Agregar el tag en `src/page/pages/config/page.php`:
   ```php
   ['key' => 'openai', 'title' => 'OpenAI'],
   ```
3. Los datos se guardan automáticamente en `IACON_CONFIG['openai']`.

### IACON_AI (`src/ai/ai.php`)

| Método | Descripción |
|---|---|
| `sendPrompt($PROMPT)` | Envía prompt a Gemini `generateContent` |
| `getModels()` | Lista modelos Gemini que soportan `generateContent` |
| `parseJson($dataString)` | Parsea JSON de respuesta de IA |
| `request($url, $method, $data)` | Llamada HTTP cURL |

### Flujo de Models
1. Usuario ingresa API Key y guarda.
2. `IACON_AI::getModels()` consulta `GET https://generativelanguage.googleapis.com/v1/models?key={apiKey}`.
3. Filtra solo modelos con `supportedGenerationMethods` que incluya `generateContent`.
4. Guarda `list_modelos` en config y muestra `<select>`.

---

## Páginas Admin

| Slug | Archivo page.php | Secciones |
|---|---|---|
| `IACON_config` | `src/page/pages/config/page.php` | `gemini.php` |
| `IACON_test` | `src/page/pages/test/page.php` | `test.php` |

### Registro de Subpáginas
```php
add_submenu_page(IACON_KEY, $title, $menu, 'manage_options', IACON_KEY . '_slug', 'CALLBACK');
```

### Tabs con FWUPage
```php
use franciscoblancojn\wordpress_utils\FWUPage;
echo FWUPage::css();
$TAGS = [['key' => 'tab1', 'title' => 'Tab 1']];
FWUPage::tabs($TAGS, $defaultTag);
echo FWUPage::js(IACON_KEY);
```

---

## Capa de Datos (wp_options CRUD)

- `IACON_USE_DATA_BASE` es la clase base para CRUD de opciones.
- `IACON_USE_DATA_CONFIG` extiende la base con `KEY = IACON_CONFIG`.
- No uses `get_option()`/`update_option()` directamente fuera de estas clases.

---

## Logging

```php
use franciscoblancojn\wordpress_utils\FWUSystemLog;
FWUSystemLog::add(IACON_KEY, ['type' => 'event_type', 'data' => $data]);
```

Los logs se ven en Ajustes > IACON_LOG en el admin.

---

## Filtros Disponibles

| Filtro | Propósito |
|---|---|
| `ia_conector_ai_mock_response` | Devuelve respuesta simulada sin llamar a la API |
| `ia_conector_ai_prompt` | Modifica el prompt antes de enviarlo |
| `ia_conector_ai_api_url` | Modifica la URL de la API |
| `ia_conector_ai_request_data` | Modifica el payload de la petición |
| `ia_conector_ai_before_request` | Acción antes de enviar la petición |
| `ia_conector_ai_after_request` | Acción después de recibir la respuesta |
| `ia_conector_ai_parse_raw` | Modifica el texto antes de parsear JSON |
| `ia_conector_ai_parsed_data` | Modifica el array ya parseado |
