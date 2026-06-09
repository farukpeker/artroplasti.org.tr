<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

echo "Polylang translated post types:\n";
if (function_exists('pll_is_translated_post_type')) {
    foreach (['congresses', 'webinars', 'courses', 'events', 'post', 'page'] as $pt) {
        echo "  {$pt}: " . (pll_is_translated_post_type($pt) ? 'YES' : 'NO') . "\n";
    }
}

echo "\nPolylang options (post_types):\n";
$pll_opts = get_option('polylang');
echo "  post_types: " . json_encode($pll_opts['post_types'] ?? []) . "\n";

echo "\nEN congress post count via WP_Query:\n";
$q = new WP_Query([
    'post_type'      => 'congresses',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'lang'           => 'en',
]);
echo "  found: " . $q->found_posts . "\n";

echo "\nTR congress post count:\n";
$q2 = new WP_Query([
    'post_type'      => 'congresses',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'lang'           => 'tr',
]);
echo "  found: " . $q2->found_posts . "\n";

echo "\nEN congress archive URL:\n";
$archive_link = get_post_type_archive_link('congresses');
echo "  default: " . $archive_link . "\n";
if (function_exists('pll_get_post_type_archive_link')) {
    echo "  pll EN: " . pll_get_post_type_archive_link('congresses', 'en') . "\n";
    echo "  pll TR: " . pll_get_post_type_archive_link('congresses', 'tr') . "\n";
}
