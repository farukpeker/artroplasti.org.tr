<?php
require dirname(__DIR__) . '/wp-load.php';

// Check page ID 66
$page = get_post(66);
echo "Page: " . $page->post_title . "\n";
echo "Template: " . get_post_meta(66, '_wp_page_template', true) . "\n";
echo "Slug: " . $page->post_name . "\n\n";

// Check what the single-webinars permalink looks like
$webinar_ids = [32, 34, 1807, 1808, 1809, 1810, 1811, 1812, 1813, 1814];
echo "Webinar permalinks:\n";
foreach ($webinar_ids as $id) {
    $p = get_post($id);
    echo $id . " | " . get_permalink($id) . " | slug: " . $p->post_name . "\n";
}

// Check CPT registration
$cpt = get_post_type_object('webinars');
echo "\nCPT rewrite: ";
print_r($cpt->rewrite);
echo "Has archive: " . ($cpt->has_archive ? 'yes: '.$cpt->has_archive : 'no') . "\n";
echo "Publicly queryable: " . ($cpt->publicly_queryable ? 'yes' : 'no') . "\n";
