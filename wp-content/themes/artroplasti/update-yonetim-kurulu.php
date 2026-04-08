<?php
/**
 * Yönetim Kurulu Sayfasını Manuel Güncelleme Scripti
 */

// WordPress'i yükle
require_once(__DIR__ . '/../../../wp-load.php');

// CLI'dan çalıştırılıyorsa veya admin ise izin ver
if (php_sapi_name() === 'cli' || (is_user_logged_in() && current_user_can('manage_options'))) {
    // İzin verildi
} else {
    die('Bu scripti çalıştırma yetkiniz yok.');
}

// HTML dosyasını oku
$html_file = 'd:/xampp/htdocs/artroplasti.org.tr/artroplasti.org.tr/artroplasti/yonetim-kurulu.html';
$html = file_get_contents($html_file);

// Yönetim Kurulu div'ini direkt al
$start_pos = strpos($html, '<div class="yk-board inset-lg-left-40" lang="tr">');
$end_pos = strpos($html, '<!-- /Yönetim Kurulu (Dernek) -->');

if ($start_pos !== false && $end_pos !== false) {
    // İçeriği al
    $content = substr($html, $start_pos, $end_pos - $start_pos);
    
    // inset-lg-left-40 class'ını kaldır (gereksiz)
    $content = str_replace('class="yk-board inset-lg-left-40"', 'class="yk-board"', $content);
    
    echo "✓ İçerik bulundu: " . strlen($content) . " karakter\n";
} else {
    die('❌ Yönetim Kurulu içeriği bulunamadı! Start: ' . $start_pos . ', End: ' . $end_pos);
}

// Görsel yollarını düzelt
$content = str_replace('../images/', '/wp-content/uploads/images/', $content);
$content = str_replace('../upload/', '/wp-content/uploads/', $content);
$content = preg_replace('/http:\/\/yonetim\.citius\.technology\/haber\/([^"\']+)/', '/wp-content/uploads/images/$1', $content);
$content = preg_replace('/https:\/\/yonetim\.citius\.technology\/files\/([^"\']+)/', '/wp-content/uploads/images/$1', $content);

// Gereksiz boşlukları temizle
$content = trim($content);

// Yönetim Kurulu sayfasını bul
$page = get_page_by_title('Yönetim Kurulu');

if (!$page) {
    die('Yönetim Kurulu sayfası bulunamadı!');
}

// Sayfayı güncelle
$result = wp_update_post(array(
    'ID' => $page->ID,
    'post_content' => $content,
));

if ($result) {
    echo "✅ Yönetim Kurulu sayfası başarıyla güncellendi!\n";
    echo "Sayfa ID: {$page->ID}\n";
    echo "İçerik uzunluğu: " . strlen($content) . " karakter\n";
    echo "\nSayfayı görüntüle: http://localhost/artroplasti.org.tr/yonetim-kurulu\n";
} else {
    echo "❌ Sayfa güncellenemedi!\n";
}
