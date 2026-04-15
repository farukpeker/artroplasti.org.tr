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
$fallbackImage = '';
foreach ($argv as $arg) {
    if (strpos($arg, '--limit=') === 0) {
        $limit = max(1, (int) substr($arg, 8));
        continue;
    }

    if (strpos($arg, '--fallback-image=') === 0) {
        $fallbackImage = trim((string) substr($arg, 17));
    }
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

function candidate_from_html(string $htmlPath): ?string
{
    if (!file_exists($htmlPath)) {
        return null;
    }

    $raw = file_get_contents($htmlPath);
    if (!$raw) {
        return null;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($raw);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $imageNode = $xpath->query('//img[@src]')->item(0);
    if (!$imageNode) {
        return null;
    }

    $src = trim($imageNode->getAttribute('src'));
    $srcPath = parse_url($src, PHP_URL_PATH);
    $basename = $srcPath ? basename($srcPath) : basename($src);

    return $basename ?: null;
}

function find_best_by_title(string $title, array $imageFiles): ?string
{
    $titleSlug = sanitize_title($title);
    $bestFile = null;
    $bestScore = 0;

    foreach ($imageFiles as $file) {
        $name = pathinfo($file, PATHINFO_FILENAME);
        $nameSlug = sanitize_title($name);

        if ($nameSlug === '') {
            continue;
        }

        similar_text($titleSlug, $nameSlug, $pct);

        if (strpos($nameSlug, $titleSlug) !== false || strpos($titleSlug, $nameSlug) !== false) {
            $pct += 10;
        }

        if ($pct > $bestScore) {
            $bestScore = $pct;
            $bestFile = $file;
        }
    }

    return $bestScore >= 62 ? $bestFile : null;
}

$imageFiles = array_values(array_filter(scandir($imageDir), function ($entry) use ($imageDir) {
    $path = $imageDir . '/' . $entry;
    if (!is_file($path)) {
        return false;
    }
    return preg_match('/\.(jpg|jpeg|png|webp)$/i', $entry) === 1;
}));

$posts = get_posts([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_key' => '_import_source_html',
    'orderby' => 'ID',
    'order' => 'ASC',
]);

$processed = 0;
$assigned = 0;
$fallbackAssigned = 0;
$skipped = 0;

foreach ($posts as $post) {
    if ($processed >= $limit) {
        break;
    }

    if (get_post_thumbnail_id($post->ID)) {
        continue;
    }

    $sourceHtml = get_post_meta($post->ID, '_import_source_html', true);
    if (!$sourceHtml) {
        $skipped++;
        continue;
    }

    $sourcePath = $htmlDir . '/' . $sourceHtml;
    $candidate = candidate_from_html($sourcePath);
    $matchFile = null;

    if ($candidate && file_exists($imageDir . '/' . $candidate)) {
        $matchFile = $candidate;
    } else {
        $matchFile = find_best_by_title($post->post_title, $imageFiles);
    }

    if (!$matchFile && $fallbackImage !== '' && file_exists($imageDir . '/' . $fallbackImage)) {
        $matchFile = $fallbackImage;
    }

    if (!$matchFile) {
        $processed++;
        $skipped++;
        echo "SKIP {$post->ID} | {$post->post_title} | eşleşme yok\n";
        continue;
    }

    $attachmentId = import_local_image_as_attachment($imageDir . '/' . $matchFile, (int) $post->ID);
    if ($attachmentId > 0) {
        set_post_thumbnail((int) $post->ID, $attachmentId);
        $assigned++;
        if ($fallbackImage !== '' && $matchFile === $fallbackImage) {
            $fallbackAssigned++;
        }
        echo "OK {$post->ID} | {$post->post_title} | {$matchFile} | thumb {$attachmentId}\n";
    } else {
        $skipped++;
        echo "SKIP {$post->ID} | {$post->post_title} | import edilemedi\n";
    }

    $processed++;
}

echo "Tamamlandı | limit={$limit} | işlenen={$processed} | atanan={$assigned} | fallback_atanan={$fallbackAssigned} | atlanan={$skipped}\n";
