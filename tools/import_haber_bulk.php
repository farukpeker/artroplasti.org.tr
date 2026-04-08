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

$baseDir = $root . '/wp-content/uploads/haberler';
$htmlDir = $baseDir . '/haber-icerik';
$imageDir = $baseDir . '/haber-gorsel';

$limit = 50;
foreach ($argv as $arg) {
    if (strpos($arg, '--limit=') === 0) {
        $limit = max(1, (int) substr($arg, 8));
    }
}

function get_inner_html(DOMNode $element): string
{
    $innerHtml = '';
    foreach ($element->childNodes as $child) {
        $innerHtml .= $element->ownerDocument->saveHTML($child);
    }
    return $innerHtml;
}

function clean_content_html(string $html): string
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    foreach ($xpath->query('//*[@style]') as $node) {
        $node->removeAttribute('style');
    }

    foreach ($xpath->query('//*[@class]') as $node) {
        $node->removeAttribute('class');
    }

    foreach ($xpath->query('//*[@data-olk-copy-source]') as $node) {
        $node->removeAttribute('data-olk-copy-source');
    }

    return trim($dom->saveHTML());
}

function import_local_image_as_attachment(string $filePath, int $postId): int
{
    if (!file_exists($filePath)) {
        return 0;
    }

    $filename = basename($filePath);

    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => '_import_source_filename',
                'value' => $filename,
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]);

    if (!empty($existing)) {
        return (int) $existing[0];
    }

    $bits = wp_upload_bits($filename, null, file_get_contents($filePath));
    if (!empty($bits['error'])) {
        return 0;
    }

    $filetype = wp_check_filetype($filename, null);

    $attachment = [
        'post_mime_type' => $filetype['type'] ?? 'image/jpeg',
        'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
        'post_parent' => $postId,
    ];

    $attachmentId = wp_insert_attachment($attachment, $bits['file'], $postId);
    if (is_wp_error($attachmentId)) {
        return 0;
    }

    $metadata = wp_generate_attachment_metadata($attachmentId, $bits['file']);
    wp_update_attachment_metadata($attachmentId, $metadata);
    update_post_meta($attachmentId, '_import_source_filename', $filename);

    return (int) $attachmentId;
}

if (!is_dir($htmlDir)) {
    echo "HTML klasörü bulunamadı: {$htmlDir}\n";
    exit(1);
}

$htmlFiles = glob($htmlDir . '/haber*.html');
if (empty($htmlFiles)) {
    echo "Import edilecek HTML dosyası bulunamadı.\n";
    exit(0);
}

natsort($htmlFiles);
$htmlFiles = array_values($htmlFiles);

$imported = 0;
$skipped = 0;
$results = [];

foreach ($htmlFiles as $htmlPath) {
    if ($imported >= $limit) {
        break;
    }

    $fileName = basename($htmlPath);

    $already = get_posts([
        'post_type' => 'post',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => '_import_source_html',
                'value' => $fileName,
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]);

    if (!empty($already)) {
        $skipped++;
        continue;
    }

    $rawHtml = file_get_contents($htmlPath);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($rawHtml);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $titleNode = $xpath->query('//title')->item(0);
    $title = $titleNode ? trim($titleNode->textContent) : pathinfo($fileName, PATHINFO_FILENAME);
    if ($title === '') {
        $title = pathinfo($fileName, PATHINFO_FILENAME);
    }

    $contentNode = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " bg-white ")]')->item(0);
    $contentHtml = $contentNode ? get_inner_html($contentNode) : '';
    $contentHtml = clean_content_html($contentHtml);

    $postId = wp_insert_post([
        'post_title' => $title,
        'post_content' => $contentHtml,
        'post_status' => 'publish',
        'post_type' => 'post',
    ], true);

    if (is_wp_error($postId) || !$postId) {
        $results[] = "ERR {$fileName} (post oluşturulamadı)";
        continue;
    }

    update_post_meta($postId, '_import_source_html', $fileName);

    $attachmentId = 0;
    $imageNode = $xpath->query('//img[@src]')->item(0);
    if ($imageNode) {
        $src = trim($imageNode->getAttribute('src'));
        $srcPath = parse_url($src, PHP_URL_PATH);
        $basename = $srcPath ? basename($srcPath) : basename($src);
        $localImagePath = $imageDir . '/' . $basename;

        if (file_exists($localImagePath)) {
            $attachmentId = import_local_image_as_attachment($localImagePath, (int) $postId);
            if ($attachmentId > 0) {
                set_post_thumbnail((int) $postId, $attachmentId);
            }
        }
    }

    $imported++;
    $results[] = "OK {$fileName} -> PostID {$postId}" . ($attachmentId ? " (thumb {$attachmentId})" : " (thumb yok)");
}

echo "Bulk import tamamlandı\n";
echo "- İstenen limit: {$limit}\n";
echo "- Import edilen: {$imported}\n";
echo "- Atlanan (zaten import): {$skipped}\n";

foreach ($results as $line) {
    echo "- {$line}\n";
}
