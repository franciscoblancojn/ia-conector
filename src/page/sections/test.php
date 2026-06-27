<?php

use franciscoblancojn\wordpress_utils\FWURespond;
use franciscoblancojn\wordpress_utils\FWUSystemLog;

$ais = [];
if (!empty($CONFIG['gemini']['apikey']) && ($CONFIG['gemini']['enabled'] ?? false)) {
    $ais[] = ['key' => 'gemini', 'title' => 'Gemini'];
}
if ($CONFIG['kodee']['enabled'] ?? false) {
    $ais[] = ['key' => 'kodee', 'title' => 'Hostinger Kodee'];
}

$resultado = '';
if (isset($_POST['save']) && $_POST['save'] == 'test') {
    $ia = sanitize_text_field($_POST['ia'] ?? '');
    $prompt = sanitize_textarea_field($_POST['prompt'] ?? '');

    FWUSystemLog::add(IACON_KEY, [
        'type' => 'test_prompt',
        'ia' => $ia,
        'prompt' => $prompt,
    ]);

    if ($ia === 'gemini') {
        $response = IACON_AI::sendPrompt($prompt);
        if ($response['status'] === 'ok') {
            $resultado = $response['data'];
        } else {
            $resultado = 'Error: ' . ($response['message'] ?? 'Error desconocido');
        }
    } else {
        $resultado = 'IA no configurada: ' . $ia;
    }

    FWUSystemLog::add(IACON_KEY, [
        'type' => 'test_result',
        'ia' => $ia,
        'prompt' => $prompt,
        'resultado' => $resultado,
    ]);
}
?>
<form method="post">
    <?php FWURespond::render($respond ?? null) ?>
    <input type="hidden" name="save" value="test">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="ia">Seleccionar IA</label>
            </th>
            <td>
                <select id="ia" name="ia" class="regular-text">
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($ais as $ai): ?>
                        <option value="<?= esc_attr($ai['key']) ?>" <?= (isset($_POST['ia']) && $_POST['ia'] === $ai['key']) ? 'selected' : '' ?>>
                            <?= esc_html($ai['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($ais)): ?>
                    <p class="description">No hay IAs configuradas. Ve a <a href="<?= admin_url('admin.php?page=' . IACON_KEY . '_config') ?>">Configuración</a> primero.</p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="prompt">Prompt</label>
            </th>
            <td>
                <textarea
                    id="prompt"
                    name="prompt"
                    rows="10"
                    class="large-text"
                    style="font-family:monospace"><?= esc_textarea($_POST['prompt'] ?? '') ?></textarea>
            </td>
        </tr>
    </table>

    <div class="content-btn">
        <button
            type="submit"
            name="submit"
            value="Enviar"
            class="button button-primary">
            Enviar
        </button>
    </div>
</form>

<?php if ($resultado): ?>
    <hr style="margin: 20px 0">
    <h2>Resultado</h2>
    <div style="background:#f0f0f1;padding:15px;border:1px solid #ccc;border-radius:4px;max-height:500px;overflow:auto;white-space:pre-wrap;word-break:break-word;font-family:monospace">
        <?= esc_html($resultado) ?>
    </div>
<?php endif; ?>
