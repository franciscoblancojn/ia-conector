<?php

use franciscoblancojn\wordpress_utils\FWUPage;

echo FWUPage::css();

$IACON_USE_DATA_CONFIG = new IACON_USE_DATA_CONFIG();
$CONFIG = $IACON_USE_DATA_CONFIG->get();

?>
<div id="page-<?= IACON_KEY ?>" class="wrap">
    <h1>Pruebas de IA</h1>
    <?php require_once IACON_DIR . 'src/page/sections/test.php'; ?>
</div>
<?php

echo FWUPage::js(IACON_KEY);
