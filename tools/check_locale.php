<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

echo "Default locale: " . get_locale() . "\n";
echo "Site language: " . get_bloginfo('language') . "\n\n";

// Check Polylang language list
if (function_exists('pll_languages_list')) {
    $langs = pll_languages_list(['fields' => ['locale', 'slug', 'name']]);
    echo "Polylang languages:\n";
    foreach ($langs as $lang) {
        echo "  slug={$lang['slug']} locale={$lang['locale']} name={$lang['name']}\n";
    }
}

// Check what translation files WordPress tries to load for this theme
echo "\nTheme languages dir contents:\n";
foreach (glob(get_template_directory() . '/languages/*') as $f) {
    echo "  " . basename($f) . "\n";
}

// Try switching and loading
echo "\nTesting with en_US:\n";
switch_to_locale('en_US');
load_theme_textdomain('artroplasti', get_template_directory() . '/languages');
echo "Locale after switch: " . get_locale() . "\n";
echo "  'Detaylar' => '" . __('Detaylar', 'artroplasti') . "'\n";

echo "\nTesting with en_GB:\n";
restore_current_locale();
switch_to_locale('en_GB');
load_theme_textdomain('artroplasti', get_template_directory() . '/languages');
echo "Locale after switch: " . get_locale() . "\n";
echo "  'Detaylar' => '" . __('Detaylar', 'artroplasti') . "'\n";
