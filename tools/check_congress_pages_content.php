<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

foreach ([64, 1895] as $id) {
    $p = get_post($id);
    echo "=== ID:{$id} slug={$p->post_name} lang=" . pll_get_post_language($id) . " ===\n";
    echo "Template: " . (get_post_meta($id, '_wp_page_template', true) ?: '(default)') . "\n";
    echo "Content:\n" . $p->post_content . "\n\n";
}
