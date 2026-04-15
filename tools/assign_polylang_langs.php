<?php
/**
 * assign_polylang_langs.php
 *
 * 1. Import edilen tüm haberlere Polylang dili atar:
 *    - _import_source_html numarası < 24566  → Türkçe (tr)
 *    - _import_source_html numarası >= 24566  → İngilizce (en)
 *
 * 2. Başlık benzerliğine göre TR ↔ EN postları çeviri olarak bağlar.
 *
 * Kullanım:
 *   php tools/assign_polylang_langs.php
 *   php tools/assign_polylang_langs.php --dry-run   (değişiklik yapmadan raporlar)
 *   php tools/assign_polylang_langs.php --link-only  (sadece bağlantı adımını çalıştırır)
 */

if (php_sapi_name() !== 'cli') {
    echo "CLI only.\n";
    exit(1);
}

$dryRun   = in_array('--dry-run',   $argv);
$linkOnly = in_array('--link-only', $argv);

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    echo "HATA: Polylang fonksiyonları bulunamadı.\n";
    exit(1);
}

$langs = pll_languages_list(['fields' => 'slug']);
if (!in_array('tr', $langs) || !in_array('en', $langs)) {
    echo "HATA: 'tr' ve 'en' dilleri Polylang'da tanımlı değil.\n";
    echo "Mevcut diller: " . implode(', ', $langs) . "\n";
    exit(1);
}

echo ($dryRun ? "[DRY-RUN] " : "") . "Tüm import postları yükleniyor...\n";

$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => '_import_source_html',
]);

echo "Toplam post: " . count($posts) . "\n\n";

// ─── ADIM 1: Dil atama ──────────────────────────────────────────────────────

$tr_posts = []; // haber_num => post_id
$en_posts = []; // haber_num => post_id
$lang_set_tr = 0;
$lang_set_en = 0;
$already_ok  = 0;

if (!$linkOnly) {
    echo "── Adım 1: Dil atama ──\n";

    foreach ($posts as $post) {
        $source = get_post_meta($post->ID, '_import_source_html', true);
        $num    = (int) preg_replace('/[^0-9]/', '', $source);
        $lang   = ($num < 24566) ? 'tr' : 'en';

        if ($lang === 'tr') {
            $tr_posts[$num] = $post->ID;
        } else {
            $en_posts[$num] = $post->ID;
        }

        $currentLang = pll_get_post_language($post->ID);
        if ($currentLang === $lang) {
            $already_ok++;
            continue;
        }

        if (!$dryRun) {
            pll_set_post_language($post->ID, $lang);
        }

        if ($lang === 'tr') {
            $lang_set_tr++;
        } else {
            $lang_set_en++;
        }
        echo ($dryRun ? "DRY " : "OK  ") . "{$lang} | {$post->ID} | {$source} | {$post->post_title}\n";
    }

    echo "\nDil atandı → TR: {$lang_set_tr}  EN: {$lang_set_en}  Zaten_atanmış: {$already_ok}\n\n";
} else {
    // link-only modda tr/en listelerini dil meta'sından doldur
    foreach ($posts as $post) {
        $source = get_post_meta($post->ID, '_import_source_html', true);
        $num    = (int) preg_replace('/[^0-9]/', '', $source);
        if ($num < 24566) {
            $tr_posts[$num] = $post->ID;
        } else {
            $en_posts[$num] = $post->ID;
        }
    }
}

// ─── ADIM 2: Çeviri bağlantısı ─────────────────────────────────────────────

echo "── Adım 2: Çeviri bağlantısı ──\n";

// TR post başlıklarını bir haritaya al
$tr_titles = []; // post_id => sanitized title
foreach ($tr_posts as $num => $id) {
    $tr_titles[$id] = strtolower(trim(get_the_title($id)));
}

$linked   = 0;
$unlinked = 0;
$THRESHOLD = 60; // minimum benzerlik puanı (%)

ksort($en_posts);

foreach ($en_posts as $en_num => $en_id) {
    $en_title = strtolower(trim(get_the_title($en_id)));

    // Mevcut çeviri bağlantısı varsa atla
    $existing = pll_get_post_translations($en_id);
    if (!empty($existing['tr']) && $existing['tr'] !== $en_id) {
        echo "SKIP (zaten bağlı) | EN:{$en_id} ↔ TR:{$existing['tr']}\n";
        continue;
    }

    // Başlık benzerliğiyle en yakın TR postu bul
    $bestId    = null;
    $bestScore = 0;

    foreach ($tr_titles as $tr_id => $tr_title) {
        similar_text($en_title, $tr_title, $pct);
        if ($pct > $bestScore) {
            $bestScore = $pct;
            $bestId    = $tr_id;
        }
    }

    if ($bestId && $bestScore >= $THRESHOLD) {
        if (!$dryRun) {
            pll_save_post_translations(['tr' => $bestId, 'en' => $en_id]);
        }
        $linked++;
        echo ($dryRun ? "DRY " : "OK  ") . "LINK | EN:{$en_id} \"{$en_title}\" ↔ TR:{$bestId} [{$bestScore}%]\n";
    } else {
        $unlinked++;
        $bestLabel = $bestId ? "en_iyi:{$bestScore}%" : "eşleşme_yok";
        echo "SKIP (düşük_benzerlik) | EN:{$en_id} \"{$en_title}\" | {$bestLabel}\n";
    }
}

echo "\nBağlandı: {$linked}  Bağlanamadı: {$unlinked}\n";
echo "TR toplam: " . count($tr_posts) . "  EN toplam: " . count($en_posts) . "\n";
echo "\nTamamlandı.\n";
