<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

echo "WordPress version: " . get_bloginfo('version') . "\n";
echo "PHP version: " . phpversion() . "\n\n";

// Check where WordPress looks for theme translations
$theme_dir = get_template_directory();
echo "Theme dir: " . $theme_dir . "\n";
echo "Languages dir: " . $theme_dir . "/languages\n\n";

// List files in languages dir
$lang_dir = $theme_dir . '/languages';
echo "Files in languages dir:\n";
foreach (glob($lang_dir . '/*') as $f) {
    echo "  " . basename($f) . " (" . filesize($f) . " bytes, " . date('Y-m-d H:i:s', filemtime($f)) . ")\n";
}

echo "\n";

// Read first 28 bytes of .mo to check magic number
$mo = $lang_dir . '/artroplasti-en_GB.mo';
$data = file_get_contents($mo, false, null, 0, 28);
$magic = unpack('V', substr($data, 0, 4))[1];
printf("MO magic: 0x%08x (expected: 0x950412de)\n", $magic);
$revision = unpack('V', substr($data, 4, 4))[1];
$count    = unpack('V', substr($data, 8, 4))[1];
echo "MO revision: {$revision}\n";
echo "MO string count: {$count}\n\n";

// Check if WordPress 6.5+ uses l10n.php
echo "Checking for l10n.php format...\n";
$l10n = $lang_dir . '/artroplasti-en_GB.l10n.php';
echo "l10n.php exists: " . (file_exists($l10n) ? 'YES' : 'NO') . "\n";

// Check WP_LANG_DIR
echo "\nWP_LANG_DIR: " . WP_LANG_DIR . "\n";
foreach (glob(WP_LANG_DIR . '/themes/artroplasti*') as $f) {
    echo "  Global lang: " . basename($f) . "\n";
}
