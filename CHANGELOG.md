# Changelog

> Todas las versiones del plugin IA Conector.

---

## [0.0.0] — 2026

- Versión inicial del plugin.
- Página de configuración con tabs (tags) para conectar distintos servicios de IA.
- Integración con Google Gemini: API Key, selector de modelos vía `getModels()`.
- Cliente HTTP cURL para Gemini API (`IACON_AI`).
- Subpágina de Pruebas: selector de IA, textarea de prompt, envío y visualización de resultado.
- Sistema de logs vía `FWUSystemLog`.
- Capa de datos basada en `wp_options` (`IACON_USE_DATA_BASE`).
- Auto-updater vía GitHub (`FWUUpdate`).
