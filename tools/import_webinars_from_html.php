<?php

if (php_sapi_name() !== 'cli') {
    echo "This script must run from CLI.\n";
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/wp-load.php';

$sourceFile = $root . '/artroplasti.org.tr/artroplasti/webinarlar.html';
$dryRun = false;

foreach ($argv as $arg) {
    if ($arg === '--dry-run') {
        $dryRun = true;
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

// Each webinar group: <li class="kc-grid"> with <a class="kc-link">
$items = $xpath->query('//li[contains(@class,"kc-grid")]');

if (!$items || $items->length === 0) {
    echo "No webinar items found in HTML.\n";
    exit(1);
}

echo "Found {$items->length} webinar group items.\n\n";

// ─── import loop ─────────────────────────────────────────────────────────────

$processed = 0;
$imported  = 0;
$updated   = 0;
$skipped   = 0;
$failed    = 0;

foreach ($items as $li) {
    $anchor = $xpath->query('.//a[@class="kc-link"]', $li)->item(0);
    if (!($anchor instanceof DOMElement)) {
        $skipped++;
        continue;
    }

    // Title from <strong> inside anchor (strip nbsp etc.)
    $strongNode = $xpath->query('.//strong', $anchor)->item(0);
    $rawTitle = $strongNode
        ? normalize_text($strongNode->textContent)
        : normalize_text($anchor->textContent);

    // Remove any trailing non-breaking spaces / extra chars
    $rawTitle = trim($rawTitle, " \t\n\r\0\x0B\xc2\xa0");

    if ($rawTitle === '') {
        $skipped++;
        continue;
    }

    // Extract year from title: e.g. "2024 EĞİTİM WEBİNARLARI" → 2024
    $year = 0;
    if (preg_match('/\b(20\d{2})\b/', $rawTitle, $m)) {
        $year = (int) $m[1];
    }

    $processed++;

    if ($dryRun) {
        $yearStr = $year > 0 ? "  [yıl: {$year}]" : '';
        echo "IMPORT: {$rawTitle}{$yearStr}\n";
        continue;
    }

    // ── dedup by year value ──
    $existingByYear = [];
    if ($year > 0) {
        $existingByYear = get_posts([
            'post_type'      => 'webinars',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'meta_query'     => [[
                'key'     => 'webinar_year',
                'value'   => $year,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ]],
            'fields' => 'ids',
        ]);
    }

    $postId   = 0;
    $isUpdate = false;

    if (!empty($existingByYear)) {
        $postId   = (int) $existingByYear[0];
        $isUpdate = true;
    } else {
        $existingByTitle = get_page_by_title($rawTitle, OBJECT, 'webinars');
        if ($existingByTitle instanceof WP_Post) {
            $postId   = (int) $existingByTitle->ID;
            $isUpdate = true;
        }
    }

    // ── insert / update ──
    $postDate = $year > 0 ? "{$year}-01-01 00:00:00" : '';

    if ($isUpdate) {
        $result = wp_update_post([
            'ID'          => $postId,
            'post_title'  => $rawTitle,
            'post_status' => 'publish',
        ], true);

        if (is_wp_error($result)) {
            $failed++;
            echo "ERR: {$rawTitle} (update failed)\n";
            continue;
        }
        $updated++;
    } else {
        $args = [
            'post_type'   => 'webinars',
            'post_title'  => $rawTitle,
            'post_status' => 'publish',
        ];
        if ($postDate !== '') {
            $args['post_date'] = $postDate;
        }

        $postId = wp_insert_post($args, true);

        if (is_wp_error($postId) || !$postId) {
            $failed++;
            echo "ERR: {$rawTitle} (insert failed)\n";
            continue;
        }
        $postId = (int) $postId;
        $imported++;
    }

    if ($year > 0) {
        update_post_meta($postId, 'webinar_year', $year);
    }
    update_post_meta($postId, '_import_source_file', basename($sourceFile));

    $verb = $isUpdate ? 'UPDATED' : 'IMPORTED';
    echo "{$verb}: {$rawTitle}" . ($year > 0 ? "  [yıl: {$year}]" : '') . " (ID {$postId})\n";
}

echo "\nDone.\n";
echo "- Source: {$sourceFile}\n";
echo "- Processed: {$processed}\n";
echo "- Imported: {$imported}\n";
echo "- Updated: {$updated}\n";
echo "- Skipped: {$skipped}\n";
echo "- Failed: {$failed}\n";
