<?php

add_action('admin_menu', function () {
    add_menu_page(
        'IA Conector Configuración',
        'IA Conector',
        'manage_options',
        IACON_KEY,
        'IACON_REDIRECT_FIRST_SUBMENU',
        'dashicons-admin-site'
    );
});
function IACON_REDIRECT_FIRST_SUBMENU()
{
    wp_redirect(admin_url('admin.php?page=' . IACON_KEY . '_config'));
    exit;
}
