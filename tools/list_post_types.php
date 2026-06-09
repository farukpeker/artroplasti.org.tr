<?php
require dirname(__DIR__) . '/wp-load.php';

// Find what post types exist
$types = get_post_types([], 'names');
echo "Post types: " . implode(', ', $types) . "\n\n";

// Search by title keyword
global $wpdb;
$rows = $wpdb->get_results(
    "SELECT ID, post_type, post_status, post_title, post_date
     FROM {$wpdb->posts}
     WHERE post_title LIKE '%webinar%' OR post_title LIKE '%Webinar%'
     ORDER BY post_date ASC
     LIMIT 20"
);
echo "Posts with 'webinar' in title:\n";
foreach ($rows as $r) {
    echo $r->ID . ' [' . $r->post_type . '/' . $r->post_status . '] ' . $r->post_title . ' (' . substr($r->post_date,0,10) . ")\n";
}
