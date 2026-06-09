<?php
require dirname(__DIR__) . '/wp-load.php';

// Try all webinar posts regardless of language
$posts = get_posts([
    'post_type'        => 'webinar',
    'post_status'      => 'any',
    'posts_per_page'   => -1,
    'suppress_filters' => true,
]);

echo "Total webinar posts: " . count($posts) . "\n\n";

foreach ($posts as $p) {
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($p->ID) : 'unknown';
    echo $p->ID . ' [' . $lang . '] ' . $p->post_status . ' | ' . $p->post_title . "\n";
    if ($p->post_excerpt) {
        echo "  EXCERPT: " . substr(strip_tags($p->post_excerpt), 0, 120) . "\n";
    }
    if ($p->post_content) {
        echo "  CONTENT: " . substr(strip_tags($p->post_content), 0, 200) . "\n";
    }
    echo "\n";
}
