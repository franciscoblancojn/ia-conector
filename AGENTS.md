# IA Conector — Reglas para IAs

Este archivo contiene las reglas, validaciones y convenciones que toda IA debe seguir al programar en este proyecto.

---

## 1. Estándares de Código

### PHP
- **WordPress Coding Standards**: Sigue los estándares de codificación de WordPress para PHP.
- **PHP 7.0+**: No uses sintaxis moderna de PHP (nullsafe `?->`, named arguments, match, readonly properties, etc). El operador `??` (null coalescing) está permitido.
- **Nombrado**: Las clases usan prefijo `IACON_` (ej: `IACON_AI`, `IACON_USE_DATA_CONFIG`). Métodos y propiedades en `camelCase` o `UPPER_SNAKE` para constantes.
- **Sanitización**: Toda salida de datos debe escaparse. Usa `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` según contexto.
- **Nonces**: Todo formulario y AJAX debe verificar nonce con `wp_verify_nonce()` o `check_ajax_referer()`.
- **Capabilities**: Toda operación admin debe verificar `current_user_can()`.

### JavaScript
- **ES5**: El plugin soporta WordPress 5.0+, usa ES5 (no arrow functions, no let/const, no template literals).
- **jQuery**: Usa `jQuery(function($){ ... })` para DOM ready.
- **AJAX**: Toda llamada AJAX debe usar `jQuery.ajax()` con action registrada via `wp_ajax_*` y nonce.
- **Nombrado**: Funciones en `snake_case` con prefijo `ia_conector_` (ej: `ia_conector_save_config`).

### CSS
- **Prefijo**: Todas las clases CSS deben llevar prefijo `ia-conector-`.
- **Especificidad**: Evita `!important`. Usa clases con la especificidad adecuada.

---

## 2. Arquitectura del Plugin

### Sistema de Archivos
- `index.php` → Plugin header y constantes globales. No agregues lógica aquí.
- `src/_.php` → Cargador maestro. Todo nuevo módulo debe ser require desde aquí.
- `src/ai/` → Clientes IA (Google Gemini).
- `src/data/` → Capa de datos (wp_options CRUD).
- `src/page/` → Páginas admin.

### Constantes
Usa las constantes definidas en `index.php`:
- `IACON_KEY` para prefijos de opciones y slugs
- `IACON_CONFIG` para la configuración global
- Nunca hardcodees strings como `'IACON'` o `'IACON_CONFIG'`

### wp_options
Toda opción global debe usar `IACON_USE_DATA_BASE` o una subclase. No uses `add_option()`/`update_option()` directamente fuera de la capa data.

---

## 3. Validaciones de Seguridad

1. **Nunca** hardcodees API keys en el código. Las API keys se guardan en `IACON_CONFIG`.
2. **Siempre** sanitiza input: `$_POST`, `$_GET`, `$_REQUEST` deben pasar por `sanitize_text_field()`, `intval()`, etc.
3. **Siempre** valida nonces en handlers AJAX (`check_ajax_referer('ia_conector_nonce', 'nonce')`).
4. **Siempre** valida capabilities: `current_user_can('manage_options')` antes de cualquier operación.

---

## 4. Convenciones del Proyecto

### AJAX Endpoints
- Action registrada: `wp_ajax_{action}` donde action usa prefijo `ia_conector_`.
- Nonce action: `ia_conector_nonce`.
- Los handlers deben estar en `src/api/`.
- Respuesta siempre en JSON: `wp_send_json_success($data)` o `wp_send_json_error($message)`.

### Logging
- Usa siempre `FWUSystemLog::add(IACON_KEY, $message)` para errores de IA.
- No uses `error_log()`, `var_dump()`, `print_r()` en producción.

---

## 5. Lo que NO debes hacer

- ✗ NO modifiques `index.php` (plugin header) sin autorización.
- ✗ NO elimines el prefijo `IACON_` de ninguna clase/función.
- ✗ NO agregues dependencias npm/composer sin autorización explícita.
- ✗ NO edites archivos en `libs/` (vendor de Composer).
- ✗ NO uses sintaxis moderna de PHP (>=7.0) — el plugin requiere PHP 7.0+.
- ✗ NO hardcodees URLs o paths — usa `IACON_URL`, `IACON_DIR`.
- ✗ NO añadas archivos nuevos sin require desde `src/_.php` o desde subcarpetas `src/*/_.php`.
