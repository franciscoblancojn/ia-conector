<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;
use franciscoblancojn\wordpress_utils\FWURespond;
use franciscoblancojn\wordpress_utils\FWUTooltip;

if (isset($_POST['save']) && $_POST['save'] == 'gemini') {
    FWUSystemLog::add(IACON_KEY, [
        'type' => 'save_gemini_config',
        'data' => $_POST,
    ]);

    $gemini = $CONFIG['gemini'] ?? [];
    $gemini['apikey'] = sanitize_text_field($_POST['apikey'] ?? '');
    if (isset($_POST['modelo'])) {
        $gemini['modelo'] = sanitize_text_field($_POST['modelo']);
    }
    $gemini['list_modelos'] = $gemini['list_modelos'] ?? null;

    if (!empty($gemini['apikey']) && $gemini['list_modelos'] == null) {
        $respond = IACON_AI::getModels();
        if ($respond['status'] == 'ok') {
            $gemini['list_modelos'] = $respond['data'] ?? [];
        }
    }

    $CONFIG['gemini'] = $gemini;
    $IACON_USE_DATA_CONFIG->set($CONFIG);
}

$gemini = $CONFIG['gemini'] ?? [];
?>
<form method="post">
    <?php FWURespond::render($respond ?? null) ?>
    <input type="hidden" name="save" value="gemini">
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php FWUTooltip::render('API Key', 'API key de Gemini para generar contenido con IA.') ?>
            </th>
            <td>
                <input
                    type="password"
                    id="apikey"
                    name="apikey"
                    placeholder="API KEY"
                    value="<?= esc_attr($gemini['apikey'] ?? '') ?>"
                    class="regular-text" />
            </td>
        </tr>

        <?php
        if (isset($gemini['list_modelos']) && count($gemini['list_modelos']) > 0) {
            $modelos = $gemini['list_modelos'];
            $modeloActual = $gemini['modelo'] ?? ($modelos[0]['model'] ?? null);
        ?>
            <tr>
                <th scope="row">
                    <?php FWUTooltip::render('Modelo', 'Modelo de IA que se usa para generar contenido.') ?>
                </th>
                <td>
                    <select id="modelo" name="modelo" class="regular-text">
                        <?php foreach ($modelos as $model):
                            $value = $model['model'];
                            $label = $model['displayName'];
                        ?>
                            <option value="<?= esc_attr($value) ?>" <?= $modeloActual === $value ? 'selected' : '' ?>>
                                <?= esc_html($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        <?php
        } elseif (!empty($gemini['apikey'])) {
        ?>
            <tr>
                <th scope="row">
                    <?php FWUTooltip::render('Modelo', 'Modelo de IA que se usa para generar contenido.') ?>
                </th>
                <td>
                    <input
                        type="text"
                        id="modelo"
                        name="modelo"
                        placeholder="Modelo (ej: gemini-2.0-flash)"
                        value="<?= esc_attr($gemini['modelo'] ?? '') ?>"
                        class="regular-text" />
                    <p class="description">Guarda la API Key para cargar la lista de modelos disponibles.</p>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>

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
