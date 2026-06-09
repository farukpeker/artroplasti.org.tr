<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

// Simulate what the archive page main query does when lang=en
// Polylang sets lang via query var
set_query_var('lang', 'en');

// Simulate the archive query
$q = new WP_Query([
    'post_type'      => 'congresses',
    'posts_per_page' => get_option('posts_per_page'),
    'post_status'    => 'publish',
]);
echo "Simulated archive query (no lang arg):\n";
echo "  SQL: " . $q->request . "\n\n";
echo "  found: " . $q->found_posts . "\n";
if ($q->have_posts()) {
    while ($q->have_posts()) {
        $q->the_post();
        $lang = function_exists('pll_get_post_language') ? pll_get_post_language(get_the_ID()) : '?';
        echo "  - ID:" . get_the_ID() . " lang={$lang} title=" . get_the_title() . "\n";
    }
}
wp_reset_postdata();

echo "\n\nDirect EN query:\n";
$q2 = new WP_Query([
    'post_type'      => 'congresses',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'lang'           => 'en',
]);
echo "  found: " . $q2->found_posts . "\n";
if ($q2->have_posts()) {
    while ($q2->have_posts()) {
        $q2->the_post();
        echo "  - ID:" . get_the_ID() . " title=" . get_the_title() . "\n";
    }
}
