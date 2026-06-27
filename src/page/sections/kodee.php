<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;
use franciscoblancojn\wordpress_utils\FWURespond;
use franciscoblancojn\wordpress_utils\FWUTooltip;

if (isset($_POST['save']) && $_POST['save'] == 'kodee') {
    FWUSystemLog::add(IACON_KEY, [
        'type' => 'save_kodee_config',
        'data' => $_POST,
    ]);

    $kodee = $CONFIG['kodee'] ?? [];
    $kodee['enabled'] = isset($_POST['enabled']);

    $CONFIG['kodee'] = $kodee;
    $IACON_USE_DATA_CONFIG->set($CONFIG);

    $respond = ['status' => 'ok', 'message' => 'Configuración de Hostinger Kodee guardada correctamente.'];
}

$kodee = $CONFIG['kodee'] ?? [];
?>
<form method="post">
    <?php FWURespond::render($respond ?? null) ?>
    <input type="hidden" name="save" value="kodee">
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php FWUTooltip::render('Activar', 'Activa o desactiva Hostinger Kodee para que esté disponible para otros plugins.') ?>
            </th>
            <td>
                <input
                    type="checkbox"
                    id="enabled"
                    name="enabled"
                    <?= ($kodee['enabled'] ?? false) ? 'checked' : '' ?>
                    class="regular-text" />
                <label for="enabled">
                    Hostinger Kodee activado
                </label>
            </td>
        </tr>
    </table>

    <div class="notice notice-info inline">
        <p>
            <strong>Hostinger Kodee</strong> es el asistente de IA integrado en el ecosistema Hostinger.
            Está disponible automáticamente en entornos Hostinger y no requiere configuración adicional.
            Actívalo para que otros plugins puedan usarlo.
        </p>
    </div>

    <div class="content-btn">
        <button
            type="submit"
            name="submit"
            value="Guardar"
            class="button button-primary">
            Guardar
        </button>
    </div>
</form>
