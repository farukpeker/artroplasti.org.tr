<?php

if (php_sapi_name() !== 'cli') {
    echo "This script must run from CLI.\n";
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/wp-load.php';

$sourceFile = $root . '/artroplasti.org.tr/artroplasti/kurslar.html';
$dryRun = false;
$limit = 0;

foreach ($argv as $arg) {
    if ($arg === '--dry-run') {
        $dryRun = true;
    }
    if (strpos($arg, '--limit=') === 0) {
        $limit = max(0, (int) substr($arg, 8));
    }
    if (strpos($arg, '--source=') === 0) {
        $candidate = trim((string) substr($arg, 9));
        if ($candidate !== '') {
            $sourceFile = preg_match('#^[A-Za-z]:\\\\|^/#', $candidate)
                ? $candidate
                : $root . '/' . ltrim(str_replace('\\', '/', $candidate), '/');
        }
    }
}

if (!file_exists($sourceFile)) {
    echo "Source file not found: {$sourceFile}\n";
    exit(1);
}

// ─── helpers ─────────────────────────────────────────────────────────────────

function normalize_text(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', trim($text));
    return $text ?? '';
}

function build_course_key(string $title, string $url): string
{
    return md5(trim($title) . '|' . trim($url));
}

/**
 * Given a full title like "11. Temel Artroplasti Kursu (02-03.10.2015)"
 * returns ['name' => '11. Temel Artroplasti Kursu', 'date' => '02-03.10.2015']
 */
function parse_course_title(string $raw): array
{
    $raw = normalize_text($raw);
    // Date pattern: parenthesised substring at the end
    if (preg_match('/^(.*?)\s*\(([^)]+)\)\s*$/', $raw, $m)) {
        return [
            'name' => trim($m[1]),
            'date' => trim($m[2]),
        ];
    }
    return ['name' => $raw, 'date' => ''];
}

// ─── parse HTML ──────────────────────────────────────────────────────────────

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$html = file_get_contents($sourceFile);
$loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
libxml_clear_errors();

if (!$loaded) {
    echo "Failed to parse HTML.\n";
    exit(1);
}

$xpath = new DOMXPath($dom);

// Each course is a <li class="kc-grid"> containing an <a class="kc-link">
$items = $xpath->query('//li[contains(@class,"kc-grid")]');

if (!$items || $items->length === 0) {
    echo "No course items found in HTML.\n";
    exit(1);
}

echo "Found {$items->length} course items.\n\n";

// ─── import loop ─────────────────────────────────────────────────────────────

$processed = 0;
$imported  = 0;
$updated   = 0;
$skipped   = 0;
$failed    = 0;

foreach ($items as $li) {
    if ($limit > 0 && $processed >= $limit) {
        break;
    }

    // Get the anchor
    $anchor = $xpath->query('.//a[@href]', $li)->item(0);
    if (!($anchor instanceof DOMElement)) {
        $skipped++;
        continue;
    }

    $href = normalize_text($anchor->getAttribute('href'));
    if ($href === '') {
        $skipped++;
        continue;
    }

    // Get title from <span> inside anchor, or fall back to anchor text
    $spanNode = $xpath->query('.//span', $anchor)->item(0);
    $rawTitle = $spanNode ? normalize_text($spanNode->textContent) : normalize_text($anchor->textContent);

    if ($rawTitle === '') {
        $skipped++;
        continue;
    }

    $parsed = parse_course_title($rawTitle);
    $title  = $parsed['name'];
    $date   = $parsed['date'];

    $processed++;

    if ($dryRun) {
        $line = "IMPORT: {$title}";
        if ($date !== '') {
            $line .= "  [{$date}]";
        }
        $line .= "  →  {$href}";
        echo $line . "\n";
        continue;
    }

    // ── dedup: check by import key ──
    $sourceKey = build_course_key($title, $href);

    $existingByKey = get_posts([
        'post_type'      => 'courses',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_query'     => [[
            'key'     => '_import_source_course_key',
            'value'   => $sourceKey,
            'compare' => '=',
        ]],
        'fields' => 'ids',
    ]);

    $postId   = 0;
    $isUpdate = false;

    if (!empty($existingByKey)) {
        $postId   = (int) $existingByKey[0];
        $isUpdate = true;
    } else {
        $existingByTitle = get_page_by_title($title, OBJECT, 'courses');
        if ($existingByTitle instanceof WP_Post) {
            $postId   = (int) $existingByTitle->ID;
            $isUpdate = true;
        }
    }

    // ── insert / update ──
    if ($isUpdate) {
        $result = wp_update_post([
            'ID'          => $postId,
            'post_title'  => $title,
            'post_status' => 'publish',
        ], true);

        if (is_wp_error($result)) {
            $failed++;
            echo "ERR: {$title} (update failed)\n";
            continue;
        }
        $updated++;
    } else {
        $postId = wp_insert_post([
            'post_type'   => 'courses',
            'post_title'  => $title,
            'post_status' => 'publish',
        ], true);

        if (is_wp_error($postId) || !$postId) {
            $failed++;
            echo "ERR: {$title} (insert failed)\n";
            continue;
        }
        $postId = (int) $postId;
        $imported++;
    }

    update_post_meta($postId, '_import_source_course_key', $sourceKey);
    update_post_meta($postId, '_import_source_file', basename($sourceFile));
    update_post_meta($postId, 'course_external_url', esc_url_raw($href));
    if ($date !== '') {
        update_post_meta($postId, 'course_date', $date);
    }

    $verb = $isUpdate ? 'UPDATED' : 'IMPORTED';
    $suffix = $date !== '' ? "  [{$date}]" : '';
    echo "{$verb}: {$title}{$suffix} (ID {$postId})\n";
}

echo "\nDone.\n";
echo "- Source: {$sourceFile}\n";
echo "- Processed: {$processed}\n";
echo "- Imported: {$imported}\n";
echo "- Updated: {$updated}\n";
echo "- Skipped: {$skipped}\n";
echo "- Failed: {$failed}\n";
