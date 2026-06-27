<?php

add_action('admin_menu', function () {
    add_submenu_page(
        IACON_KEY,
        'Pruebas de IA',
        'Pruebas',
        'manage_options',
        IACON_KEY . '_test',
        'IACON_PAGE_TEST_VIEW'
    );
});

function IACON_PAGE_TEST_VIEW()
{
    require_once IACON_DIR . 'src/page/pages/test/page.php';
}
