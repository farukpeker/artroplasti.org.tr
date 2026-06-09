<?php
if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }
require_once dirname(__DIR__) . '/wp-load.php';

// Force English
switch_to_locale('en_GB');
load_theme_textdomain('artroplasti', get_template_directory() . '/languages');

$tests = [
    'Eğitim Yılları',
    'Detaylar',
    'Webinarlar',
    'EĞİTİM WEBİNARLARI',
    'Webinar bulunamadı.',
    'Bu yılda webinar bulunamadı.',
    'Anasayfa',
];

echo "Testing translations (locale: " . get_locale() . ")\n\n";
foreach ($tests as $str) {
    $translated = __($str, 'artroplasti');
    $status = ($translated !== $str) ? 'OK' : 'MISSING';
    echo "[{$status}] '{$str}' => '{$translated}'\n";
}

// Check .mo file exists and size
$mo = get_template_directory() . '/languages/artroplasti-en_GB.mo';
echo "\n.mo file: " . (file_exists($mo) ? filesize($mo) . " bytes" : "NOT FOUND") . "\n";
echo ".mo mtime: " . date('Y-m-d H:i:s', filemtime($mo)) . "\n";

// Clear translation cache
wp_cache_flush();
echo "\nCache flushed.\n";
