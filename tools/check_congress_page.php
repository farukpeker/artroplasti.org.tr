<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

// Check if there's a page conflicting with the congress archive
$pages = get_posts([
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'post_status'    => ['publish','draft'],
    'lang'           => '',
]);

echo "Pages that might conflict with congress archive:\n";
foreach ($pages as $p) {
    $slug = $p->post_name;
    if (in_array($slug, ['kongreler','congresses','congress','kongre'])) {
        $lang = function_exists('pll_get_post_language') ? pll_get_post_language($p->ID) : '?';
        echo "  ID:{$p->ID} slug={$slug} lang={$lang} status={$p->post_status} title='{$p->post_title}'\n";
        echo "  template: " . get_post_meta($p->ID, '_wp_page_template', true) . "\n";
        echo "  content: " . substr($p->post_content, 0, 100) . "\n\n";
    }
}

// Check rewrite rules for congresses
$rules = get_option('rewrite_rules');
echo "\nRewrite rules for kongreler:\n";
foreach ($rules as $pattern => $query) {
    if (strpos($pattern, 'kongreler') !== false) {
        echo "  {$pattern} => {$query}\n";
    }
}

// What does the EN menu link to for congresses?
echo "\nMenu items pointing to congress archive:\n";
$menus = wp_get_nav_menus();
foreach ($menus as $menu) {
    $items = wp_get_nav_menu_items($menu->term_id);
    if (!$items) continue;
    foreach ($items as $item) {
        if (stripos($item->url, 'kongre') !== false || stripos($item->title, 'kongre') !== false || stripos($item->title, 'congress') !== false) {
            echo "  Menu '{$menu->name}': '{$item->title}' => {$item->url}\n";
        }
    }
}
