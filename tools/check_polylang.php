<?php

if (php_sapi_name() !== 'cli') {
    echo "CLI only.\n";
    exit(1);
}

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_languages_list')) {
    echo "HATA: Polylang aktif değil.\n";
    exit(1);
}

echo "Polylang aktif.\n";

$langs = pll_languages_list(['fields' => 'slug']);
echo "Tanımlı diller: " . implode(', ', $langs) . "\n";

if (!in_array('tr', $langs) || !in_array('en', $langs)) {
    echo "UYARI: 'tr' ve/veya 'en' slug'u eksik. Lütfen Polylang ayarlarından bu dilleri ekleyin.\n";
} else {
    echo "TR ve EN dilleri tamam.\n";
}
