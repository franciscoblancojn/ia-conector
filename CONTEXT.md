# IA Conector — Contexto para IAs

> Plug-in WordPress v0.0.0 — Generado automáticamente para que IAs entren en contexto rápido.

---

## ¿Qué hace este plugin?

Plugin de conexión con múltiples IAs. Proporciona una página de configuración para conectar distintos servicios de IA (Gemini, OpenAI, etc.) y exponerlos para otros plugins.

- Página de configuración con tabs por cada IA
- Integración con Google Gemini (API Key + selector de modelos)
- Cliente HTTP para Gemini API
- Subpágina de pruebas para enviar prompts y ver resultados
- Sistema de logs vía `FWUSystemLog`
- Auto-update vía GitHub

---

## Constantes globales

| Constante | Valor | Dónde se usa |
|---|---|---|
| `IACON_KEY` | `'IACON'` | Prefijo de opciones, meta keys, slugs |
| `IACON_CONFIG` | `'IACON_CONFIG'` | `wp_options` → configuración del plugin |
| `IACON_DIR` | `plugin_dir_path(__FILE__)` | Base del plugin |
| `IACON_URL` | `plugin_dir_url(__FILE__)` | URL base del plugin |
| `IACON_KEY_SEPARETE` | `'____IACON____'` | Separador en valores de formularios |
| `IACON_LOG` | `true` | Habilita logs del plugin |
| `IACON_LOG_KEY` | `'IACON_LOG'` | Clave para opción de logs |
| `IACON_LOG_COUNT` | `100` | Máximo de entradas de log |
| `IACON_BASENAME` | `plugin_basename(__FILE__)` | Base name del plugin |

---

## Estructura de archivos

```
index.php               → Plugin header, constantes, auto-updater GitHub (vía Composer)
libs/                   → Composer vendor (franciscoblancojn/wordpress_utils)
src/
  _.php                 → Cargador maestro (require de todos los módulos)
  ai/
    ai.php              → IACON_AI: Cliente HTTP Google Gemini
  data/
    base.php            → IACON_USE_DATA_BASE: CRUD genérico wp_options
    config.php          → IACON_USE_DATA_CONFIG: Config plugin
  page/
    add.php             → add_menu_page('IA Conector')
    pages/
      config/           → Submenú "Configuración"
      test/             → Submenú "Pruebas"
    sections/
      gemini.php        → API Key, modelo Gemini, selector de modelos
      test.php          → Pruebas de prompt con IA
```

---

## Clases y métodos clave

### IACON_AI (`src/ai/ai.php`)
| Método | Descripción |
|---|---|
| `sendPrompt($PROMPT)` | Envía prompt a Gemini `generateContent`, devuelve texto |
| `getModels()` | Lista modelos Gemini que soportan `generateContent` |
| `parseJson($dataString)` | Parsea JSON de respuesta de IA (limpia ```json, extrae JSON válido) |
| `request($url, $method, $data)` | Llamada HTTP cURL a Gemini API |

### IACON_USE_DATA_BASE (`src/data/base.php`)
| Método | Descripción |
|---|---|
| `get()` | Retorna todo el array de datos |
| `set($DATA)` | Reemplaza todos los datos y guarda |
| `setField($key, $value)` | Actualiza un campo específico |
| `add($DATA)` | Mergea datos nuevos con existentes |

---

## wp_options Keys

| Option Key | Clase | Propósito |
|---|---|---|
| `IACON_CONFIG` | `IACON_USE_DATA_CONFIG` | Config global: apikey, modelo, list_modelos por IA |

---

## IA: Google Gemini

- **API**: `https://generativelanguage.googleapis.com/v1/models/{model}:generateContent?key={apiKey}`
- **Modelos**: `https://generativelanguage.googleapis.com/v1/models?key={apiKey}`
- **Modelo**: Configurable (primero que soporte `generateContent` por defecto)
- **Config**: `maxOutputTokens: 65536`, `temperature: 0.2`
- **Timeout**: 300 segundos

---

## Dependencias

- **WordPress** 5.0+
- **PHP** 5.6+
- **Google Gemini API Key** (obligatorio para usar Gemini)
- **Composer**: `franciscoblancojn/wordpress_utils` (FWUSystemLog, FWUPage, FWUUpdate, FWURespond, FWUTooltip)
