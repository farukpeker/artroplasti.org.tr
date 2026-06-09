<?php
/**
 * Copies webinar_year meta from TR webinar posts to their EN translations.
 *
 * Usage:
 *   php tools/fix_webinar_en_meta.php --dry-run
 *   php tools/fix_webinar_en_meta.php
 */

if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }

$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_get_post_translations')) {
    echo "ERROR: Polylang not available.\n"; exit(1);
}

$tr_webinars = get_posts([
    'post_type'      => 'webinars',
    'posts_per_page' => -1,
    'lang'           => 'tr',
]);

echo "Found " . count($tr_webinars) . " TR webinar posts.\n\n";

foreach ($tr_webinars as $tr_post) {
    $year = get_post_meta($tr_post->ID, 'webinar_year', true);

    $translations = pll_get_post_translations($tr_post->ID);
    $en_id = $translations['en'] ?? null;

    if (!$en_id) {
        echo "SKIP: TR:{$tr_post->ID} '{$tr_post->post_title}' — no EN translation found.\n";
        continue;
    }

    $existing_year = get_post_meta($en_id, 'webinar_year', true);

    if ($existing_year) {
        echo "OK:   TR:{$tr_post->ID} → EN:{$en_id} already has webinar_year={$existing_year}\n";
        continue;
    }

    echo "FIX:  TR:{$tr_post->ID} → EN:{$en_id} '{$tr_post->post_title}' — copying webinar_year={$year}\n";

    if (!$dryRun && $year) {
        update_post_meta($en_id, 'webinar_year', $year);

        // Also copy any other webinar-related meta
        $meta_keys = ['webinar_video_url', 'webinar_date', 'webinar_description'];
        foreach ($meta_keys as $key) {
            $val = get_post_meta($tr_post->ID, $key, true);
            if ($val) {
                update_post_meta($en_id, $key, $val);
            }
        }
    }
}

echo "\nDone.\n";
