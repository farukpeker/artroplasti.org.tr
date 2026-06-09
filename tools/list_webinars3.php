<?php
require dirname(__DIR__) . '/wp-load.php';

$ids = [1814, 1813, 1812, 1811, 1810, 1809, 1808, 1807, 32, 34];

foreach ($ids as $id) {
    $p    = get_post($id);
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($id) : '?';
    $tr   = function_exists('pll_get_post_translations') ? pll_get_post_translations($id) : [];
    $en   = isset($tr['en']) ? 'EN:' . $tr['en'] : 'NO_EN';
    echo "ID:{$id} [{$lang}] {$en}\n";
    echo "Title: " . $p->post_title . "\n";
    echo "Slug : " . $p->post_name . "\n";
    echo "Excerpt: " . substr(strip_tags($p->post_excerpt), 0, 150) . "\n";
    echo "Content: " . substr(strip_tags($p->post_content), 0, 300) . "\n";
    echo str_repeat('-', 60) . "\n";
}
