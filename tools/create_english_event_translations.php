<?php
/**
 * Creates missing English translations for homepage slider items and their
 * linked content. Safe to run more than once.
 *
 * Usage:
 *   php tools/create_english_event_translations.php --dry-run
 *   php tools/create_english_event_translations.php
 */

if (php_sapi_name() !== 'cli') {
    echo "CLI only.\n";
    exit(1);
}

$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    echo "ERROR: Polylang functions are not available.\n";
    exit(1);
}

$langs = pll_languages_list(['fields' => 'slug']);
if (!in_array('tr', $langs, true) || !in_array('en', $langs, true)) {
    echo "ERROR: Polylang must have both tr and en languages.\n";
    exit(1);
}

$translations = [
    1875 => [
        'title' => 'Istanbul Monthly Arthroplasty Meeting',
        'excerpt' => 'The Istanbul Monthly Arthroplasty Meeting brings colleagues together for current discussions in hip and knee arthroplasty.',
        'content' => '<p>The Istanbul Monthly Arthroplasty Meeting is organized for physicians and healthcare professionals interested in hip and knee arthroplasty. The meeting provides a focused setting to review current cases, exchange clinical experience, and discuss practical approaches in arthroplasty practice.</p><p>Participants can follow the latest updates shared by the Turkish Arthroplasty Association and access the meeting details through this page.</p>',
    ],
    1867 => [
        'title' => 'Ankara Monthly Arthroplasty Meeting',
        'excerpt' => 'The Ankara Monthly Arthroplasty Meeting offers a professional program focused on current arthroplasty practice.',
        'content' => '<p>The Ankara Monthly Arthroplasty Meeting is prepared for specialists, residents, and healthcare professionals working in the field of hip and knee arthroplasty. The program creates an opportunity to discuss current topics, case-based experiences, and practical clinical approaches.</p><p>Meeting details, announcements, and related updates can be followed through this page.</p>',
    ],
    1855 => [
        'title' => 'Istanbul Monthly Arthroplasty Meeting',
        'excerpt' => 'A monthly scientific meeting in Istanbul focused on developments and shared experience in arthroplasty.',
        'content' => '<p>The Istanbul Monthly Arthroplasty Meeting gathers professionals interested in arthroplasty for scientific exchange and professional discussion. The meeting focuses on current developments, clinical decision-making, and shared experience in hip and knee replacement surgery.</p><p>Updated information about the meeting is available through this page.</p>',
    ],
    1779 => [
        'title' => 'Ankara Monthly Arthroplasty Meeting, Thursday, April 16',
        'excerpt' => 'The Ankara Monthly Arthroplasty Meeting on Thursday, April 16 focuses on current topics in arthroplasty.',
        'content' => '<p>The Ankara Monthly Arthroplasty Meeting, scheduled for Thursday, April 16, is planned as a professional scientific gathering for colleagues working in arthroplasty. The meeting provides space for case discussions, current clinical perspectives, and exchange of experience.</p><p>Program and participation details can be followed through this page.</p>',
    ],
    1778 => [
        'title' => '27th Basic Arthroplasty Course (May 15-16 - Ankara)',
        'excerpt' => 'The 27th Basic Arthroplasty Course in Ankara offers a structured educational program on core arthroplasty principles.',
        'content' => '<p>The 27th Basic Arthroplasty Course will be held in Ankara on May 15-16. The course is designed to support physicians who want to strengthen their knowledge of fundamental principles in hip and knee arthroplasty.</p><p>The program focuses on essential concepts, surgical planning, case evaluation, and practical decision-making in arthroplasty. Course details and related announcements can be accessed through this page.</p>',
    ],
    1777 => [
        'title' => 'KADAD Diyarbakir Monthly Arthroplasty Meeting',
        'excerpt' => 'The KADAD Diyarbakir Monthly Arthroplasty Meeting supports regional scientific exchange in arthroplasty.',
        'content' => '<p>The KADAD Diyarbakir Monthly Arthroplasty Meeting is organized to support regional education and professional communication in the field of arthroplasty. The meeting brings colleagues together for current topics, case discussions, and shared clinical experience.</p><p>Announcements and meeting details can be followed through this page.</p>',
    ],
    1776 => [
        'title' => 'KADAD Antalya Regional Meeting (Sunday, April 5, 2026)',
        'excerpt' => 'The KADAD Antalya Regional Meeting will take place on Sunday, April 5, 2026 with a focus on arthroplasty practice.',
        'content' => '<p>The KADAD Antalya Regional Meeting will take place on Sunday, April 5, 2026. The meeting is planned as a regional scientific event for professionals interested in hip and knee arthroplasty.</p><p>The program aims to encourage professional exchange, review current clinical topics, and strengthen collaboration within the arthroplasty community.</p>',
    ],
    1775 => [
        'title' => '18th Arthroplasty Winter Meeting - Uludag Karinna Hotel (April 9-12, 2026)',
        'excerpt' => 'The 18th Arthroplasty Winter Meeting will be held at Uludag Karinna Hotel on April 9-12, 2026.',
        'content' => '<p>The 18th Arthroplasty Winter Meeting will be held at Uludag Karinna Hotel on April 9-12, 2026. The meeting offers a scientific platform for discussing current developments, surgical approaches, and clinical experience in arthroplasty.</p><p>Participants can follow program information, announcements, and meeting updates through this page.</p>',
    ],
    1774 => [
        'title' => 'Direct Anterior Approach Cadaver Course',
        'excerpt' => 'The Direct Anterior Approach Cadaver Course provides focused training on anatomy, technique, and surgical planning.',
        'content' => '<p>The Direct Anterior Approach Cadaver Course is designed for physicians seeking focused training on the direct anterior approach in hip arthroplasty. The course emphasizes anatomical orientation, surgical planning, technical steps, and practical considerations.</p><p>Course announcements and participation details can be followed through this page.</p>',
    ],
];

$created = 0;
$updated = 0;
$linked = 0;

function art_en_find_existing_translation(int $sourceId): int
{
    $linked = pll_get_post($sourceId, 'en');
    if ($linked) {
        return (int) $linked;
    }

    $posts = get_posts([
        'post_type' => get_post_type($sourceId),
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_generated_en_from_post_id',
        'meta_value' => (string) $sourceId,
        'lang' => '',
    ]);

    return empty($posts) ? 0 : (int) $posts[0];
}

function art_en_copy_meta(int $sourceId, int $targetId, array $extraMeta = []): void
{
    $copyKeys = [
        '_thumbnail_id',
        'event_start_date',
        'event_end_date',
        'event_location',
        'event_city',
        'event_registration_url',
        'event_program_url',
        'blog_manual_date',
        'blog_pdf_url',
        'course_external_url',
        'congress_date',
        'congress_location',
        'congress_site_url',
        'congress_program_url',
        'congress_website_url',
    ];

    foreach ($copyKeys as $key) {
        $value = get_post_meta($sourceId, $key, true);
        if ($value !== '') {
            update_post_meta($targetId, $key, $value);
        }
    }

    foreach ($extraMeta as $key => $value) {
        update_post_meta($targetId, $key, $value);
    }
}

function art_en_create_or_update(int $sourceId, array $translation, bool $dryRun): int
{
    $source = get_post($sourceId);
    if (!$source) {
        echo "SKIP missing source {$sourceId}\n";
        return 0;
    }

    $existingId = art_en_find_existing_translation($sourceId);
    $postData = [
        'post_title' => $translation['title'],
        'post_excerpt' => $translation['excerpt'],
        'post_content' => $translation['content'],
        'post_status' => $source->post_status,
        'post_type' => $source->post_type,
        'post_author' => $source->post_author,
        'post_date' => $source->post_date,
        'post_date_gmt' => $source->post_date_gmt,
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', true),
        'menu_order' => $source->menu_order,
        'comment_status' => $source->comment_status,
        'ping_status' => $source->ping_status,
    ];

    if ($existingId) {
        $postData['ID'] = $existingId;
        if ($dryRun) {
            echo "DRY update EN {$existingId} for TR {$sourceId}: {$translation['title']}\n";
            return $existingId;
        }
        $targetId = wp_update_post(wp_slash($postData), true);
        if (is_wp_error($targetId)) {
            echo "ERROR update {$sourceId}: " . $targetId->get_error_message() . "\n";
            return 0;
        }
    } else {
        $postData['post_name'] = sanitize_title($translation['title']);
        if ($dryRun) {
            echo "DRY create EN for TR {$sourceId}: {$translation['title']}\n";
            return -$sourceId;
        }
        $targetId = wp_insert_post(wp_slash($postData), true);
        if (is_wp_error($targetId)) {
            echo "ERROR create {$sourceId}: " . $targetId->get_error_message() . "\n";
            return 0;
        }
    }

    pll_set_post_language($sourceId, 'tr');
    pll_set_post_language((int) $targetId, 'en');
    pll_save_post_translations(['tr' => $sourceId, 'en' => (int) $targetId]);
    update_post_meta((int) $targetId, '_generated_en_from_post_id', (string) $sourceId);

    return (int) $targetId;
}

function art_en_find_target_post_from_url(string $url): int
{
    $id = url_to_postid($url);
    if ($id) {
        return (int) $id;
    }

    $path = trim((string) wp_parse_url($url, PHP_URL_PATH), '/');
    if ($path === '') {
        return 0;
    }

    $slug = basename($path);
    $posts = get_posts([
        'post_type' => ['post', 'events', 'courses', 'congresses', 'webinars', 'page'],
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'name' => $slug,
        'posts_per_page' => 1,
        'fields' => 'ids',
        'lang' => '',
    ]);

    return empty($posts) ? 0 : (int) $posts[0];
}

foreach ($translations as $sliderId => $translation) {
    $slider = get_post($sliderId);
    if (!$slider || $slider->post_type !== 'banner_slide') {
        echo "SKIP {$sliderId}: not a banner slide.\n";
        continue;
    }

    $buttonUrl = (string) get_post_meta($sliderId, 'banner_button_url', true);
    $linkedSourceId = $buttonUrl ? art_en_find_target_post_from_url($buttonUrl) : 0;
    $linkedEnglishId = 0;

    if ($linkedSourceId && $linkedSourceId !== $sliderId) {
        $linkedEnglishId = art_en_create_or_update($linkedSourceId, $translation, $dryRun);
        if ($linkedEnglishId > 0) {
            art_en_copy_meta($linkedSourceId, $linkedEnglishId);
            $GLOBALS['updated']++;
        }
    }

    $sliderEnglishId = art_en_create_or_update($sliderId, $translation, $dryRun);
    if ($sliderEnglishId > 0) {
        $targetUrl = $linkedEnglishId > 0 ? get_permalink($linkedEnglishId) : $buttonUrl;
        art_en_copy_meta($sliderId, $sliderEnglishId, [
            'banner_button_text' => 'Read More',
            'banner_button_url' => $targetUrl,
        ]);
        $GLOBALS['updated']++;
    }
}

if (!$dryRun) {
    clean_post_cache(0);
}

echo ($dryRun ? "[DRY-RUN] " : "") . "Done.\n";
