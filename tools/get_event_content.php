<?php
require dirname(__DIR__) . '/wp-load.php';
$ids = [1876, 1854, 1829, 1828];
foreach ($ids as $id) {
    $p = get_post($id);
    echo "=== ID:{$id} ===\n";
    echo "Title: " . $p->post_title . "\n";
    echo "Date: " . $p->post_date . "\n";
    echo "Slug: " . $p->post_name . "\n";
    echo "Content:\n" . $p->post_content . "\n";
    echo "\n\n";
}
