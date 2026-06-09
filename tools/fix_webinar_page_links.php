<?php
/**
 * Fixes the Webinarlar page (ID 66) by replacing old hardcoded links
 * with correct CPT permalinks, and adds 2025 which was missing.
 *
 * Usage:
 *   php tools/fix_webinar_page_links.php --dry-run
 *   php tools/fix_webinar_page_links.php
 */

if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }

$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

// Webinar CPT IDs by year (TR versions)
$webinar_map = [
    '2025' => 34,
    '2024' => 32,
    '2022' => 1807,
    '2021' => 1808,
    '2020' => 1809,
    '2019' => 1810,
    '2018' => 1811,
    '2017' => 1812,
    '2016' => 1813,
    '2015' => 1814,
];

// Build the new page content using actual get_permalink()
$items = '';
foreach ($webinar_map as $year => $post_id) {
    $permalink     = get_permalink($post_id);
    $relative_path = '/' . ltrim(str_replace(home_url(), '', $permalink), '/');
    $title         = get_the_title($post_id);
    echo "  {$year}: {$relative_path} [{$title}]\n";
    $items .= "\t<li class=\"kc-grid\"><a class=\"kc-link\" href=\"" . esc_attr($relative_path) . "\"><img src=\"http://yonetim.citius.technology/menu/menu1050/logo-dark.png\" alt=\"\" /> <strong>" . esc_html($title) . "</strong> </a></li>\n";
}

$new_content = '<div class="kadad-courses inset-lg-left-40" lang="tr">
<div class="kc-wrap">
<article class="kc-card" role="list">
<ul>
' . $items . '</ul>
</article></div>
</div>';

echo "\n=== New page content preview ===\n";
echo $new_content . "\n";

if (!$dryRun) {
    $result = wp_update_post([
        'ID'           => 66,
        'post_content' => $new_content,
    ]);

    if (is_wp_error($result)) {
        echo "ERROR: " . $result->get_error_message() . "\n";
    } else {
        echo "\nPage 66 updated successfully.\n";
    }
}
