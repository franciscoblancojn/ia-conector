<?php
/*
Plugin Name: IA Conector
Plugin URI: https://github.com/franciscoblancojn/ia-conector
Description: Plugin de conexión con múltiples IAs. Proporciona una página de configuración para conectar distintos servicios de IA (Gemini, OpenAI, etc.) y exponerlos para otros plugins.
Version: 1.1.0
Author: franciscoblancojn
Author URI: https://franciscoblanco.vercel.app/
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wc-ia-conector
*/

if (!function_exists('is_plugin_active'))
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');

require_once __DIR__ . '/libs/autoload.php';

//IACON_
define("IACON_KEY", 'IACON');
define("IACON_MODE_DEV", in_array($_SERVER['HTTP_HOST'] ?? '', ['wordpress.local', 'localhost', '127.0.0.1',]));
define("IACON_KEY_SEPARETE", '____IACON____');
define("IACON_CONFIG", 'IACON_CONFIG');
define("IACON_LOG", true);
define("IACON_LOG_KEY", "IACON_LOG");
define("IACON_LOG_COUNT", 100);
define("IACON_BASENAME", plugin_basename(__FILE__));
define("IACON_DIR", plugin_dir_path(__FILE__));
define("IACON_URL", plugin_dir_url(__FILE__));

function IACON_get_version()
{
    $plugin_data = get_plugin_data(__FILE__);
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}

use franciscoblancojn\wordpress_utils\FWUUpdate;

FWUUpdate::init([
    'basename' => IACON_BASENAME,
    'dir' => IACON_DIR,
    'file' => "index.php",
    'path_repository' => 'franciscoblancojn/ia-conector',
    'branch' => 'master',
    'token_array_split' => [
        "g",
        "h",
        "p",
        "_",
        "G",
        "4",
        "W",
        "E",
        "W",
        "F",
        "p",
        "V",
        "U",
        "E",
        "F",
        "V",
        "x",
        "F",
        "U",
        "n",
        "b",
        "M",
        "k",
        "P",
        "R",
        "x",
        "o",
        "f",
        "t",
        "Y",
        "8",
        "z",
        "j",
        "t",
        "4",
        "E",
        "x",
        "b",
        "i",
        "9"
    ]
]);

use franciscoblancojn\wordpress_utils\FWUSystemLog;

if (is_admin()) {
    FWUSystemLog::init(IACON_KEY);
    require_once IACON_DIR . 'src/_.php';
}
