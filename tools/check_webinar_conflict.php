<?php
require dirname(__DIR__) . '/wp-load.php';

// Check the Webinarlar page content
$page = get_post(66);
echo "=== PAGE 66 CONTENT ===\n";
echo $page->post_content . "\n\n";

// Check if page conflicts with CPT
global $wp_rewrite;
echo "=== REWRITE RULES (webinarlar) ===\n";
$rules = get_option('rewrite_rules');
foreach ($rules as $pattern => $query) {
    if (strpos($pattern, 'webinarlar') !== false) {
        echo $pattern . " => " . $query . "\n";
    }
}
