<?php

if (php_sapi_name() !== 'cli') {
    echo "This script must run from CLI.\n";
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$sourceFile = $root . '/artroplasti.org.tr/artroplasti/kongreler.html';
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
            if (preg_match('#^[A-Za-z]:\\\\|^/#', $candidate)) {
                $sourceFile = $candidate;
            } else {
                $sourceFile = $root . '/' . ltrim(str_replace('\\', '/', $candidate), '/');
            }
        }
    }
}

if (!file_exists($sourceFile)) {
    echo "Source file not found: {$sourceFile}\n";
    exit(1);
}

function normalize_space(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', trim($text));
    return $text ?? '';
}

function get_node_text(?DOMNode $node): string
{
    if (!$node) {
        return '';
    }

    return normalize_space($node->textContent ?? '');
}

function lower_text(string $text): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($text, 'UTF-8');
    }

    return strtolower($text);
}

function build_congress_key(string $title, string $date, string $location): string
{
    return md5(lower_text(trim($title . '|' . $date . '|' . $location)));
}

function resolve_source_url(string $url, string $sourceFilePath, string $rootPath): string
{
    $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($url === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }

    if (strpos($url, '//') === 0) {
        return 'https:' . $url;
    }

    $baseDir = dirname($sourceFilePath);
    $candidate = realpath($baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $url));
    if ($candidate && strpos($candidate, $rootPath) === 0) {
        $rel = str_replace('\\', '/', substr($candidate, strlen($rootPath)));
        return home_url('/' . ltrim($rel, '/'));
    }

    if (strpos($url, '../upload/') === 0) {
        return home_url('/artroplasti.org.tr/upload/' . ltrim(substr($url, strlen('../upload/')), '/'));
    }

    if (strpos($url, './') === 0) {
        $url = substr($url, 2);
    }

    return home_url('/artroplasti.org.tr/artroplasti/' . ltrim($url, '/'));
}

function import_local_file_as_attachment(string $filePath, int $postId, string $sourceUrl): int
{
    if (!file_exists($filePath)) {
        return 0;
    }

    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => '_import_source_url',
                'value' => $sourceUrl,
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]);

    if (!empty($existing)) {
        return (int) $existing[0];
    }

    $filename = wp_basename($filePath);
    $bits = wp_upload_bits($filename, null, file_get_contents($filePath));
    if (!empty($bits['error'])) {
        return 0;
    }

    $filetype = wp_check_filetype($filename, null);
    $attachmentId = wp_insert_attachment([
        'post_mime_type' => $filetype['type'] ?? 'image/jpeg',
        'post_title' => sanitize_text_field(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
        'post_parent' => $postId,
    ], $bits['file'], $postId);

    if (is_wp_error($attachmentId) || !$attachmentId) {
        return 0;
    }

    $metadata = wp_generate_attachment_metadata($attachmentId, $bits['file']);
    wp_update_attachment_metadata($attachmentId, $metadata);
    update_post_meta($attachmentId, '_import_source_url', $sourceUrl);

    return (int) $attachmentId;
}

function import_remote_image_as_attachment(string $url, int $postId): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => '_import_source_url',
                'value' => $url,
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]);

    if (!empty($existing)) {
        return (int) $existing[0];
    }

    $tmp = download_url($url, 60);
    if (is_wp_error($tmp)) {
        $tmp = '';
    }

    $pathPart = (string) parse_url($url, PHP_URL_PATH);
    $name = wp_basename($pathPart);
    if ($name === '') {
        $name = 'congress-image-' . time() . '.jpg';
    }

    if ($tmp !== '') {
        $fileArray = [
            'name' => sanitize_file_name($name),
            'tmp_name' => $tmp,
        ];

        $attachmentId = media_handle_sideload($fileArray, $postId);
        if (!is_wp_error($attachmentId) && $attachmentId) {
            update_post_meta($attachmentId, '_import_source_url', $url);
            return (int) $attachmentId;
        }

        @unlink($tmp);
    }

    // Fallback: fetch image body and create attachment via wp_upload_bits.
    $response = wp_remote_get($url, [
        'timeout' => 60,
        'sslverify' => false,
    ]);

    if (is_wp_error($response)) {
        return 0;
    }

    $body = wp_remote_retrieve_body($response);
    if ($body === '') {
        return 0;
    }

    $filename = sanitize_file_name($name);
    if ($filename === '') {
        $filename = 'congress-image-' . time() . '.jpg';
    }

    $bits = wp_upload_bits($filename, null, $body);
    if (!empty($bits['error'])) {
        return 0;
    }

    $filetype = wp_check_filetype($filename, null);
    $attachmentId = wp_insert_attachment([
        'post_mime_type' => $filetype['type'] ?? 'image/jpeg',
        'post_title' => sanitize_text_field(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
        'post_parent' => $postId,
    ], $bits['file'], $postId);

    if (is_wp_error($attachmentId) || !$attachmentId) {
        return 0;
    }

    $metadata = wp_generate_attachment_metadata($attachmentId, $bits['file']);
    wp_update_attachment_metadata($attachmentId, $metadata);

    update_post_meta($attachmentId, '_import_source_url', $url);
    return (int) $attachmentId;
}

function set_featured_image_from_source(string $rawSrc, string $sourceFilePath, string $rootPath, int $postId): int
{
    $resolvedUrl = resolve_source_url($rawSrc, $sourceFilePath, $rootPath);
    if ($resolvedUrl === '') {
        return 0;
    }

    if (preg_match('#^https?://#i', $resolvedUrl)) {
        $parsedPath = (string) parse_url($resolvedUrl, PHP_URL_PATH);
        $localCandidate = realpath($rootPath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $parsedPath), DIRECTORY_SEPARATOR));
        if ($localCandidate && file_exists($localCandidate)) {
            return import_local_file_as_attachment($localCandidate, $postId, $resolvedUrl);
        }

        return import_remote_image_as_attachment($resolvedUrl, $postId);
    }

    return 0;
}

function classify_links(array $links): array
{
    $site = null;
    $program = null;
    $website = null;
    $extras = [];

    foreach ($links as $link) {
        $labelLc = lower_text($link['label']);

        if ($site === null && (strpos($labelLc, 'web sitesi') !== false || strpos($labelLc, 'website') !== false)) {
            $site = $link;
            continue;
        }

        if ($program === null && strpos($labelLc, 'program') !== false) {
            $program = $link;
            continue;
        }

        if ($website === null) {
            $website = $link;
            continue;
        }

        $extras[] = $link;
    }

    return [
        'site' => $site,
        'program' => $program,
        'website' => $website,
        'extras' => $extras,
    ];
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$html = file_get_contents($sourceFile);
$loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
libxml_clear_errors();

if (!$loaded) {
    echo "Failed to parse source HTML.\n";
    exit(1);
}

$xpath = new DOMXPath($dom);
$articles = $xpath->query('//article[contains(concat(" ", normalize-space(@class), " "), " event-card ")]');

if (!$articles || $articles->length === 0) {
    echo "No congress cards found in source file.\n";
    exit(0);
}

$imported = 0;
$updated = 0;
$skipped = 0;
$failed = 0;
$processed = 0;

foreach ($articles as $article) {
    if ($limit > 0 && $processed >= $limit) {
        break;
    }

    $processed++;

    $title = get_node_text($xpath->query('.//h2[contains(@class, "event-title")]', $article)->item(0));
    if ($title === '') {
        $title = 'Kongre ' . $processed;
    }

    $date = '';
    $location = '';

    $metaNodes = $xpath->query('.//div[contains(@class, "event-meta")]', $article);
    foreach ($metaNodes as $metaNode) {
        $label = lower_text(get_node_text($xpath->query('./p[1]', $metaNode)->item(0)));
        $value = get_node_text($xpath->query('./p[2]', $metaNode)->item(0));

        if ($value === '') {
            $value = get_node_text($metaNode);
        }

        if ($date === '' && strpos($label, 'tarih') !== false) {
            $value = preg_replace('/^.*tarih\s*/iu', '', $value);
            $date = trim((string) $value);
            continue;
        }

        if ($location === '' && strpos($label, 'yer') !== false) {
            $value = preg_replace('/^.*yer\s*/iu', '', $value);
            $location = trim((string) $value);
            continue;
        }
    }

    if ($location === '') {
        $location = normalize_space((string) $article->attributes->getNamedItem('data-location')?->nodeValue);
    }

    $links = [];
    $actionLinks = $xpath->query('.//div[contains(@class, "actions")]//a[@href]', $article);
    foreach ($actionLinks as $a) {
        $rawHref = trim((string) $a->getAttribute('href'));
        $resolvedHref = resolve_source_url($rawHref, $sourceFile, $root);
        if ($resolvedHref === '') {
            continue;
        }

        $label = get_node_text($a);
        if ($label === '') {
            $label = 'Baglanti';
        }

        $links[] = [
            'label' => $label,
            'url' => $resolvedHref,
        ];
    }

    $classified = classify_links($links);

    $postContent = '';

    $excerptParts = [];
    if ($date !== '') {
        $excerptParts[] = 'Tarih: ' . $date;
    }
    if ($location !== '') {
        $excerptParts[] = 'Yer: ' . $location;
    }
    $postExcerpt = implode(' | ', $excerptParts);

    $sourceKey = build_congress_key($title, $date, $location);

    $existingByKey = get_posts([
        'post_type' => 'congresses',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => '_import_source_congress_key',
                'value' => $sourceKey,
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]);

    $postId = 0;
    $isUpdate = false;

    if (!empty($existingByKey)) {
        $postId = (int) $existingByKey[0];
        $isUpdate = true;
    } else {
        $existingByTitle = get_page_by_title($title, OBJECT, 'congresses');
        if ($existingByTitle instanceof WP_Post) {
            $postId = (int) $existingByTitle->ID;
            $isUpdate = true;
        }
    }

    if ($dryRun) {
        $action = $isUpdate ? 'UPDATE' : 'IMPORT';
        echo "{$action}: {$title}\n";
        continue;
    }

    if ($isUpdate) {
        $result = wp_update_post([
            'ID' => $postId,
            'post_title' => $title,
            'post_content' => $postContent,
            'post_excerpt' => $postExcerpt,
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
            'post_type' => 'congresses',
            'post_title' => $title,
            'post_content' => $postContent,
            'post_excerpt' => $postExcerpt,
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

    update_post_meta($postId, '_import_source_congress_key', $sourceKey);
    update_post_meta($postId, '_import_source_file', basename($sourceFile));

    update_post_meta($postId, 'congress_date', $date);
    update_post_meta($postId, 'congress_location', $location);

    $site = $classified['site'];
    update_post_meta($postId, 'congress_site_url', $site['url'] ?? '');
    update_post_meta($postId, 'congress_site_label', $site['label'] ?? 'Web sitesi');

    $program = $classified['program'];
    update_post_meta($postId, 'congress_program_url', $program['url'] ?? '');
    update_post_meta($postId, 'congress_program_label', $program['label'] ?? 'Kongre Programi');

    $website = $classified['website'];
    update_post_meta($postId, 'congress_website_url', $website['url'] ?? '');
    update_post_meta($postId, 'congress_website_label', $website['label'] ?? 'Detay');

    $extras = $classified['extras'] ?? [];
    update_post_meta($postId, 'congress_extra_links', wp_json_encode($extras, JSON_UNESCAPED_UNICODE));

    $imgNode = $xpath->query('.//div[contains(@class, "event-media")]//img[@src]', $article)->item(0);
    if ($imgNode instanceof DOMElement) {
        $imgSrc = trim((string) $imgNode->getAttribute('src'));
        if ($imgSrc !== '') {
            $attachmentId = set_featured_image_from_source($imgSrc, $sourceFile, $root, $postId);
            if ($attachmentId > 0) {
                set_post_thumbnail($postId, $attachmentId);
            }
        }
    }

    $verb = $isUpdate ? 'UPDATED' : 'IMPORTED';
    echo "{$verb}: {$title} (ID {$postId})\n";
}

echo "\nDone.\n";
echo "- Source: {$sourceFile}\n";
echo "- Processed: {$processed}\n";
echo "- Imported: {$imported}\n";
echo "- Updated: {$updated}\n";
echo "- Skipped: {$skipped}\n";
echo "- Failed: {$failed}\n";
