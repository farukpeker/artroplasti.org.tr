<?php
/**
 * Fix English Congresses:
 * 1. Updates the EN menu "Congresses" item to point to /en/kongreler/ (CPT archive)
 * 2. Updates page 1895 (EN Congresses) to show congress cards from CPT
 */

if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

// ── 1. Fix EN menu item URL ───────────────────────────────────────────────────
echo "=== Fixing EN menu item ===\n";

$menus = wp_get_nav_menus();
foreach ($menus as $menu) {
    if (stripos($menu->name, 'english') === false && stripos($menu->name, 'en') === false) continue;

    $items = wp_get_nav_menu_items($menu->term_id);
    if (!$items) continue;

    foreach ($items as $item) {
        if (stripos($item->title, 'congress') === false) continue;

        echo "  Found: Menu '{$menu->name}' → '{$item->title}' → {$item->url}\n";

        $new_url = home_url('/en/kongreler/');
        echo "  Will update to: {$new_url}\n";

        if (!$dryRun) {
            update_post_meta($item->ID, '_menu_item_url', $new_url);
            echo "  Updated.\n";
        }
    }
}

// ── 2. Update EN Congresses page content with CPT query ───────────────────────
echo "\n=== Updating page 1895 content ===\n";

// Build congress cards HTML from CPT (EN posts)
$congresses = get_posts([
    'post_type'      => 'congresses',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'lang'           => 'en',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

echo "  Found " . count($congresses) . " EN congress posts.\n";

$cards = '';
foreach ($congresses as $post) {
    $img_url  = get_the_post_thumbnail_url($post->ID, 'large') ?: artroplasti_default_thumb();
    $date     = get_post_meta($post->ID, 'congress_date', true);
    $location = get_post_meta($post->ID, 'congress_location', true);
    $title    = esc_html($post->post_title);
    $link     = get_permalink($post->ID);

    // Collect all links
    $btns = '';
    $site_url   = get_post_meta($post->ID, 'congress_site_url', true);
    $site_label = get_post_meta($post->ID, 'congress_site_label', true) ?: 'Website';
    if ($site_url) $btns .= '<a class="btn primary" target="_blank" rel="noopener noreferrer" href="' . esc_url($site_url) . '">' . esc_html($site_label) . '</a>';

    $prog_url   = get_post_meta($post->ID, 'congress_program_url', true);
    $prog_label = get_post_meta($post->ID, 'congress_program_label', true) ?: 'Congress Programme';
    if ($prog_url) $btns .= '<a class="btn" target="_blank" rel="noopener noreferrer" href="' . esc_url($prog_url) . '">' . esc_html($prog_label) . '</a>';

    $web_url   = get_post_meta($post->ID, 'congress_website_url', true);
    $web_label = get_post_meta($post->ID, 'congress_website_label', true) ?: 'Details';
    if ($web_url) $btns .= '<a class="btn" target="_blank" rel="noopener noreferrer" href="' . esc_url($web_url) . '">' . esc_html($web_label) . '</a>';

    $extra_json = get_post_meta($post->ID, 'congress_extra_links', true);
    if ($extra_json) {
        $extras = json_decode($extra_json, true);
        if (is_array($extras)) {
            foreach ($extras as $ex) {
                if (!empty($ex['url'])) {
                    $btns .= '<a class="btn" target="_blank" rel="noopener noreferrer" href="' . esc_url($ex['url']) . '">' . esc_html($ex['label'] ?? '') . '</a>';
                }
            }
        }
    }

    $btns .= '<a class="btn" href="' . esc_url($link) . '">Details</a>';

    $date_meta     = $date ? '<div class="event-meta"><p><span aria-hidden="true">📅</span> Date</p><p>' . esc_html($date) . '</p></div>' : '';
    $location_meta = $location ? '<div class="event-meta"><p><span aria-hidden="true">📍</span> Location</p><p>' . esc_html($location) . '</p></div>' : '';

    $cards .= '<article class="event-card">
<div class="event-media"><img style="width:350px" src="' . esc_url($img_url) . '" alt="' . $title . '" loading="lazy"></div>
<div class="event-body">
<h2 class="event-title display-6 mb-4 pb-3 pt-3 text-center" style="background-color:#e1f0ff">' . $title . '</h2>
' . $date_meta . $location_meta . '
<div class="actions">' . $btns . '</div>
</div></article>' . "\n";
}

$new_content = '<div class="container py-14 py-md-3">
<div class="row gx-lg-8 gx-xl-12 gy-10 mb-14 mb-md-3 align-items-center">
<div class="col-lg-12">
<section class="events">
' . $cards . '</section></div></div></div>';

echo "  Content built (" . strlen($new_content) . " chars).\n";

if (!$dryRun) {
    $result = wp_update_post([
        'ID'           => 1895,
        'post_content' => $new_content,
    ]);
    if (is_wp_error($result)) {
        echo "  ERROR: " . $result->get_error_message() . "\n";
    } else {
        echo "  Page 1895 updated.\n";
    }
}

echo "\nDone.\n";
