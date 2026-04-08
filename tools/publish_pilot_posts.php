<?php
require_once __DIR__ . '/../wp-load.php';

$postIds = [181, 183];

foreach ($postIds as $postId) {
    $result = wp_update_post([
        'ID' => $postId,
        'post_status' => 'publish',
    ], true);

    if (is_wp_error($result)) {
        echo $postId . " | ERR | " . $result->get_error_message() . PHP_EOL;
        continue;
    }

    echo $postId . " | OK | publish" . PHP_EOL;
}
