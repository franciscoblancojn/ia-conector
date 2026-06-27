<?php

add_action('admin_menu', function () {
    add_submenu_page(
        IACON_KEY,
        'Configuración de IAs',
        'Configuración',
        'manage_options',
        IACON_KEY . '_config',
        'IACON_PAGE_CONFIG_VIEW'
    );
    remove_submenu_page(IACON_KEY, IACON_KEY);
});

function IACON_PAGE_CONFIG_VIEW()
{
    require_once IACON_DIR . 'src/page/pages/config/page.php';
}
