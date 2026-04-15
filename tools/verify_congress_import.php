<?php

if (php_sapi_name() !== 'cli') {
    echo "This script must run from CLI.\n";
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/wp-load.php';

$posts = get_posts([
    'post_type' => 'congresses',
    'posts_per_page' => -1,
    'post_status' => 'publish',
]);

$total = count($posts);
$withThumb = 0;

foreach ($posts as $post) {
    if (has_post_thumbnail($post->ID)) {
        $withThumb++;
    }
}

echo "congresses={$total}\n";
echo "with_thumb={$withThumb}\n";
