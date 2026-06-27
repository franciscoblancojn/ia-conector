<?php

use franciscoblancojn\wordpress_utils\FWUPage;

echo FWUPage::css();

$IACON_USE_DATA_CONFIG = new IACON_USE_DATA_CONFIG();
$CONFIG = $IACON_USE_DATA_CONFIG->get();

$TAGS = [
    [
        'key' => 'gemini',
        'title' => 'Gemini',
    ],
];
$defaultTag = $TAGS[0]['key'];

?>
<div id="page-<?= IACON_KEY ?>" class="wrap">
    <h1>IA Conector</h1>
    <?php FWUPage::tabs($TAGS, $defaultTag); ?>
    <?php foreach ($TAGS as $value) { ?>
        <div class="tab-content <?= $value['key'] == $defaultTag ? 'nav-tab-active' : '' ?>" id="<?= $value['key'] ?>">
            <?php require_once IACON_DIR . 'src/page/sections/' . $value['key'] . '.php'; ?>
        </div>
    <?php } ?>
</div>
<?php

echo FWUPage::js(IACON_KEY);
