<?php
require_once dirname(__DIR__) . '/wp-load.php';

global $wpdb;

$tr_lang = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->term_taxonomy} tt JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id JOIN {$wpdb->terms} t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'language' AND t.slug = 'tr'");
$en_lang = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->term_taxonomy} tt JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id JOIN {$wpdb->terms} t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'language' AND t.slug = 'en'");

echo 'TR post sayisi: ' . $tr_lang . PHP_EOL;
echo 'EN post sayisi: ' . $en_lang . PHP_EOL;

// Bağlantılı çeviri sayısı
$linked = $wpdb->get_var("SELECT COUNT(DISTINCT o.object_id) FROM {$wpdb->term_relationships} o JOIN {$wpdb->term_taxonomy} tt ON o.term_taxonomy_id = tt.term_taxonomy_id JOIN {$wpdb->terms} t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'post_translations'");
echo 'Ceviri bagintisi olan post: ' . $linked . PHP_EOL;
