<?php
require dirname(__DIR__) . '/wp-load.php';

$posts = get_posts([
    'post_type'        => 'events',
    'post_status'      => 'publish',
    'posts_per_page'   => -1,
    'suppress_filters' => true,
    'orderby'          => 'date',
    'order'            => 'DESC',
]);

echo "Total: " . count($posts) . "\n\n";
foreach ($posts as $p) {
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($p->ID) : '?';
    $tr   = function_exists('pll_get_post_translations') ? pll_get_post_translations($p->ID) : [];
    $en   = isset($tr['en']) ? 'EN:' . $tr['en'] : 'NO_EN';
    echo $p->ID . ' [' . $lang . '] ' . $en . ' | ' . $p->post_title . "\n";
    echo "  Excerpt: " . substr(strip_tags($p->post_excerpt), 0, 120) . "\n";
    echo "  Content: " . substr(strip_tags($p->post_content), 0, 200) . "\n\n";
}
