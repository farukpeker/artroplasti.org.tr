<?php
require_once __DIR__ . '/../wp-load.php';

$postIds = [181, 183];

foreach ($postIds as $id) {
    $post = get_post($id);
    if (!$post) {
        echo $id . " | bulunamadı" . PHP_EOL;
        continue;
    }

    $thumbId = get_post_thumbnail_id($id);
    $thumbUrl = $thumbId ? wp_get_attachment_url($thumbId) : '';

    echo $id
        . ' | ' . $post->post_status
        . ' | ' . $post->post_title
        . ' | thumb:' . $thumbId
        . ' | ' . $thumbUrl
        . PHP_EOL;
}
