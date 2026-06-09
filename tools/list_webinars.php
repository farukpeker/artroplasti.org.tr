<?php
require dirname(__DIR__) . '/wp-load.php';
$posts = get_posts([
    'post_type'        => 'webinar',
    'post_status'      => 'publish',
    'posts_per_page'   => -1,
    'suppress_filters' => false,
    'lang'             => 'tr',
    'orderby'          => 'date',
    'order'            => 'ASC',
]);
foreach ($posts as $p) {
    $tr    = pll_get_post_translations($p->ID);
    $has_en = isset($tr['en']) ? 'EN:' . $tr['en'] : 'NO_EN';
    echo $p->ID . ' | ' . $p->post_title . ' | ' . $has_en . "\n";
    echo "  EXCERPT: " . substr(strip_tags($p->post_excerpt), 0, 100) . "\n";
    echo "  CONTENT: " . substr(strip_tags($p->post_content), 0, 200) . "\n\n";
}
