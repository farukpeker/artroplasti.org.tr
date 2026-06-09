<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

global $wpdb;

// Find Polylang language taxonomy term IDs
$pll_terms = $wpdb->get_results(
    "SELECT t.term_id, t.slug, tt.term_taxonomy_id
     FROM {$wpdb->terms} t
     JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
     WHERE tt.taxonomy = 'language'
     ORDER BY t.slug"
);
echo "Polylang language terms:\n";
foreach ($pll_terms as $term) {
    echo "  term_id={$term->term_id} slug={$term->slug} term_taxonomy_id={$term->term_taxonomy_id}\n";
}

// Find EN language term_taxonomy_id
$en_tt_id = null;
$tr_tt_id = null;
foreach ($pll_terms as $term) {
    if ($term->slug === 'en') $en_tt_id = $term->term_taxonomy_id;
    if ($term->slug === 'tr') $tr_tt_id = $term->term_taxonomy_id;
}
echo "\nEN term_taxonomy_id: {$en_tt_id}\n";
echo "TR term_taxonomy_id: {$tr_tt_id}\n\n";

// Check which EN congress posts have language term assigned
$en_congress_ids = [1961,1962,1963,1964,1965,1966,1967,1968,1969,1970,1971,1972];
echo "EN congress posts — language term assignments:\n";
foreach ($en_congress_ids as $id) {
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT term_taxonomy_id FROM {$wpdb->term_relationships}
         WHERE object_id = %d AND term_taxonomy_id IN ({$en_tt_id},{$tr_tt_id})",
        $id
    ));
    $assigned = $row ? $row->term_taxonomy_id : 'NONE';
    $lang_label = ($assigned == $en_tt_id) ? 'EN ✓' : (($assigned == $tr_tt_id) ? 'TR !' : 'MISSING !');
    echo "  ID:{$id} term_taxonomy_id={$assigned} => {$lang_label}\n";
}
