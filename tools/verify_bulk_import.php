<?php
require_once __DIR__ . '/../wp-load.php';

$query = new WP_Query([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_key' => '_import_source_html',
]);

echo 'published_with_import_meta=' . count($query->posts) . PHP_EOL;

$latest = get_posts([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 5,
    'orderby' => 'ID',
    'order' => 'DESC',
    'meta_key' => '_import_source_html',
]);

foreach ($latest as $post) {
    $source = get_post_meta($post->ID, '_import_source_html', true);
    $thumb = get_post_thumbnail_id($post->ID);
    echo $post->ID . ' | ' . $post->post_title . ' | source:' . $source . ' | thumb:' . $thumb . PHP_EOL;
}
