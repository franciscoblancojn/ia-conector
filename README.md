# IA Conector

**Version:** 1.0.0 | **License:** GPLv2+

Plugin de WordPress que proporciona una página de configuración centralizada para conectar distintos servicios de inteligencia artificial y exponerlos para que otros plugins puedan utilizarlos.

---

## ✨ Características

- 🔌 **Conexión con múltiples IAs** — Cada IA tiene su propio tab de configuración
- 🤖 **Google Gemini** — API Key, selector de modelos vía API
- 🧪 **Página de Pruebas** — Envía prompts a cualquier IA configurada y ve los resultados
- 📋 **Sistema de Logs** — Registro de actividad del plugin accesible desde la barra de administración
- 🔄 **Auto-Update vía GitHub** — El plugin se actualiza automáticamente desde GitHub Releases

---

## 📋 Requisitos

- WordPress 5.0+
- PHP 5.6+
- Clave de API de [Google Gemini](https://aistudio.google.com/) (para usar Gemini)

---

## ⚙️ Instalación

1. Descarga el plugin desde [Aquí](https://github.com/franciscoblancojn/ia-conector/archive/refs/heads/master.zip).
2. Subelo y Actívalo desde el menú **Plugins** de WordPress.
3. Ve a **IA Conector → Configuración** e ingresa tu **API Key de Gemini**.
4. ¡Listo! Ya puedes usar la conexión desde otros plugins. 🎉

---

## 🗂️ Estructura del Plugin

```
ia-conector/
├── index.php                     # Archivo principal (plugin header, constantes, updater vía Composer)
├── composer.json                 # Dependencias Composer
├── package.json                  # Scripts de release/versionado
├── libs/                         # Dependencias (Composer vendor renombrado)
├── src/
│   ├── _.php                     # Cargador maestro
│   ├── ai/                       # Capa de IA (cliente Gemini)
│   │   └── ai.php                # IACON_AI - Cliente HTTP para Gemini
│   ├── data/                     # Persistencia de datos (opciones de WP)
│   │   ├── base.php              # IACON_USE_DATA_BASE - CRUD genérico con wp_options
│   │   └── config.php            # IACON_USE_DATA_CONFIG - Configuración del plugin
│   └── page/                     # Páginas del admin
│       ├── add.php               # Registro del menú principal
│       ├── pages/
│       │   ├── config/           # Página de configuración
│       │   │   ├── add.php       # Submenú "Configuración"
│       │   │   └── page.php      # Layout con tabs por IA
│       │   └── test/             # Página de pruebas
│       │       ├── add.php       # Submenú "Pruebas"
│       │       └── page.php      # Layout de pruebas
│       └── sections/
│           ├── gemini.php        # Configuración de Gemini
│           └── test.php          # Pruebas de prompt con IA
```

---

## 🖥️ Páginas del Admin

| Menú | Slug | Descripción |
|------|------|-------------|
| ⚙️ **Configuración** | `IACON_config` | Tabs por cada IA: API Key, modelo, selector de modelos |
| 🧪 **Pruebas** | `IACON_test` | Selector de IA, textarea de prompt, envío y resultado |

---

## 🧠 Clases Principales

| Clase | Archivo | Función |
|---|---|---|
| `IACON_AI` | `src/ai/ai.php` | 🛰️ Cliente HTTP para la API de Google Gemini |
| `IACON_USE_DATA_BASE` | `src/data/base.php` | 💾 CRUD genérico basado en `wp_options` |
| `IACON_USE_DATA_CONFIG` | `src/data/config.php` | ⚙️ Configuración del plugin |

---

## 🔐 Seguridad

- ✅ Todas las API keys se almacenan en `wp_options` y se muestran como campos password
- ✅ Las capacidades requeridas son `manage_options`
- ✅ Sanitización específica por tipo de dato (`sanitize_text_field`, `esc_attr`, `esc_url`)

---

## 📄 Licencia

GPLv2+ — Ver [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) para más detalles.

---

## 👤 Developer

- **Name:** Francisco Blanco
- **Website:** https://franciscoblanco.vercel.app/
- **Email:** blancofrancisco34@gmail.com
