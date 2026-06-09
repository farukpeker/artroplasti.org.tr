<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

// All congress posts regardless of language
$posts = get_posts([
    'post_type'      => 'congresses',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'lang'           => '', // suppress Polylang language filter
]);

echo "Total congress posts (all languages): " . count($posts) . "\n\n";

foreach ($posts as $p) {
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($p->ID) : 'N/A';
    $translations = function_exists('pll_get_post_translations') ? pll_get_post_translations($p->ID) : [];
    echo "ID:{$p->ID} lang={$lang} title='{$p->post_title}'\n";
    echo "  translations: " . json_encode($translations) . "\n";
}
