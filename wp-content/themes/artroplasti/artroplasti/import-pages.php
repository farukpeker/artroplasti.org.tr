<?php
/**
 * HTML Sayfaları WordPress'e İçe Aktarma Scripti
 * 
 * Kullanım: WordPress admin panelinde wp-admin/index.php?import_html_pages=1 olarak çalıştırın
 * veya terminalde: php import-pages.php
 */

// WordPress'i yükle
require_once(__DIR__ . '/../../../wp-load.php');

// CLI'dan çalıştırılıyorsa veya admin ise izin ver
if (php_sapi_name() === 'cli' || (is_user_logged_in() && current_user_can('manage_options'))) {
    // İzin verildi
} else {
    die('Bu scripti çalıştırma yetkiniz yok. Lütfen WordPress admin olarak giriş yapın veya terminalde çalıştırın.');
}

// HTML klasör yolu
$html_dir = 'd:/xampp/htdocs/artroplasti.org.tr/artroplasti.org.tr/artroplasti/';

// Sayfa eşleştirmeleri (HTML dosyası => WordPress sayfa başlığı)
$pages = array(
    'baskandan-mesaj.html' => array(
        'title' => 'Başkandan Mesaj',
        'parent' => 'Hakkımızda'
    ),
    'tarihce.html' => array(
        'title' => 'Tarihçe',
        'parent' => 'Hakkımızda'
    ),
    'yonetim-kurulu.html' => array(
        'title' => 'Yönetim Kurulu',
        'parent' => 'Hakkımızda'
    ),
    'bilimsel-kurullar.html' => array(
        'title' => 'Bilimsel Kurullar',
        'parent' => 'Hakkımızda'
    ),
    'dernek-tuzugu.html' => array(
        'title' => 'Dernek Tüzüğü',
        'parent' => 'Hakkımızda'
    ),
    'dernek-logolari.html' => array(
        'title' => 'Dernek Logoları',
        'parent' => 'Hakkımızda'
    ),
    'kongreler.html' => array(
        'title' => 'Kongreler',
        'parent' => 'Eğitim'
    ),
    'kurslar.html' => array(
        'title' => 'Kurslar',
        'parent' => 'Eğitim'
    ),
    'webinarlar.html' => array(
        'title' => 'Webinarlar',
        'parent' => 'Eğitim'
    ),
    'basilmis-kitaplar.html' => array(
        'title' => 'Basılmış Kitaplar',
        'parent' => 'Eğitim'
    ),
    'bolgesel-toplantilar.html' => array(
        'title' => 'Bölgesel Toplantılar',
        'parent' => 'Eğitim'
    ),
    'onam-formlari.html' => array(
        'title' => 'Onam Formları',
        'parent' => 'Eğitim'
    ),
    'skorlar.html' => array(
        'title' => 'Skorlar',
        'parent' => 'Eğitim'
    ),
    'e-ogretim.html' => array(
        'title' => 'E-Öğretim',
        'parent' => 'Eğitim'
    ),
    'neden-uyelik.html' => array(
        'title' => 'Neden Üyelik?',
        'parent' => 'Üyelik'
    ),
    'nasil-uye-olunur.html' => array(
        'title' => 'Nasıl Üye Olunur?',
        'parent' => 'Üyelik'
    ),
    'doktor-bul.html' => array(
        'title' => 'Doktor Bul',
        'parent' => 'Hasta Bilgilendirme'
    ),
    'kalca-protezi.html' => array(
        'title' => 'Kalça Protezi',
        'parent' => 'Hasta Bilgilendirme'
    ),
    'diz-protezi.html' => array(
        'title' => 'Diz Protezi',
        'parent' => 'Hasta Bilgilendirme'
    ),
    'haberler.html' => array(
        'title' => 'Haberler',
        'parent' => null
    ),
    'iletisim.html' => array(
        'title' => 'İletişim',
        'parent' => null,
        'template' => 'page-contact.php'
    ),
);

/**
 * HTML içeriğini temizle ve WordPress için hazırla
 */
function clean_html_content($html) {
    // Önce asıl içerik section'ını al (wrapper bg-light içindeki)
    if (preg_match('/<section[^>]*class="[^"]*wrapper[^"]*bg-light[^"]*"[^>]*>(.*?)<\/section>/is', $html, $matches)) {
        $content = $matches[1];
    } 
    // Yoksa tüm body'yi al
    else if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
        $content = $matches[1];
        // Navigation ve header'ı çıkar
        $content = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $content);
        $content = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $content);
        $content = preg_replace('/<section[^>]*class="[^"]*wrapper[^"]*bg-soft-primary[^"]*"[^>]*>.*?<\/section>/is', '', $content);
    } else {
        $content = $html;
    }
    
    // Container div'ini al
    if (preg_match('/<div[^>]*class="[^"]*container[^"]*"[^>]*>(.*?)<\/div>\s*<!\-\-\s*\/\.container/is', $content, $matches)) {
        $content = $matches[1];
    }
    
    // Gereksiz divleri temizle
    $content = preg_replace('/<div[^>]*class="row[^"]*"[^>]*>(.*?)<\/div>\s*<!\-\-\/\.row/is', '$1', $content);
    $content = preg_replace('/<div[^>]*class="col-lg-12"[^>]*>(.*?)<\/div>\s*<!\-\-\/column/is', '$1', $content);
    
    // Görsel yollarını düzelt - hem relative hem de absolute
    $content = str_replace('../images/', '/wp-content/uploads/images/', $content);
    $content = str_replace('../upload/', '/wp-content/uploads/', $content);
    $content = preg_replace('/http:\/\/yonetim\.citius\.technology\/haber\/([^"\']+)/', '/wp-content/uploads/images/$1', $content);
    $content = preg_replace('/https:\/\/yonetim\.citius\.technology\/files\/([^"\']+)/', '/wp-content/uploads/images/$1', $content);
    
    // Relative HTML linkleri düzelt
    $content = preg_replace_callback('/href=["\']([^"\']+\.html)["\']/', function($matches) {
        $link = basename($matches[1], '.html');
        return 'href="/' . $link . '"';
    }, $content);
    
    // HTML yorumlarını temizle
    $content = preg_replace('/<!--(.|\s)*?-->/', '', $content);
    
    // Gereksiz boş divleri temizle
    $content = preg_replace('/<div[^>]*>\s*<\/div>/', '', $content);
    
    // Birden fazla boş satırı tek satıra indir
    $content = preg_replace('/\n\s*\n/', "\n", $content);
    
    // Gereksiz boşlukları temizle
    $content = trim($content);
    
    // Baştaki ve sondaki script/style taglerini temizle
    $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);
    $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
    
    return $content;
}

/**
 * Ana sayfaları oluştur
 */
function create_parent_pages() {
    $parents = array('Hakkımızda', 'Eğitim', 'Üyelik', 'Hasta Bilgilendirme');
    $parent_ids = array();
    
    foreach ($parents as $parent_title) {
        // Sayfa zaten var mı kontrol et
        $existing = get_page_by_title($parent_title);
        if ($existing) {
            $parent_ids[$parent_title] = $existing->ID;
            echo "✓ Ana sayfa mevcut: {$parent_title} (ID: {$existing->ID})<br>";
            continue;
        }
        
        // Yeni ana sayfa oluştur
        $parent_id = wp_insert_post(array(
            'post_title'   => $parent_title,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        
        if ($parent_id) {
            $parent_ids[$parent_title] = $parent_id;
            echo "✓ Ana sayfa oluşturuldu: {$parent_title} (ID: {$parent_id})<br>";
        }
    }
    
    return $parent_ids;
}

/**
 * HTML sayfalarını içe aktar
 */
function import_html_pages($pages, $html_dir, $parent_ids, $update_existing = false) {
    $imported = 0;
    $skipped = 0;
    $errors = 0;
    $updated = 0;
    
    foreach ($pages as $filename => $page_data) {
        $filepath = $html_dir . $filename;
        
        // Dosya var mı kontrol et
        if (!file_exists($filepath)) {
            echo "✗ Dosya bulunamadı: {$filename}<br>";
            $errors++;
            continue;
        }
        
        // HTML içeriğini oku
        $html = file_get_contents($filepath);
        $content = clean_html_content($html);
        
        // Parent ID'yi bul
        $parent_id = 0;
        if (isset($page_data['parent']) && $page_data['parent']) {
            $parent_id = isset($parent_ids[$page_data['parent']]) ? $parent_ids[$page_data['parent']] : 0;
        }
        
        // Sayfa zaten var mı kontrol et
        $existing = get_page_by_title($page_data['title']);
        if ($existing) {
            if ($update_existing) {
                // Mevcut sayfayı güncelle
                $post_data = array(
                    'ID'           => $existing->ID,
                    'post_content' => $content,
                    'post_parent'  => $parent_id,
                );
                
                $result = wp_update_post($post_data);
                
                if ($result) {
                    // Template varsa ata
                    if (isset($page_data['template'])) {
                        update_post_meta($existing->ID, '_wp_page_template', $page_data['template']);
                    }
                    
                    echo "✓ Sayfa güncellendi: {$page_data['title']} (ID: {$existing->ID})";
                    if ($parent_id) {
                        echo " - Alt sayfa: {$page_data['parent']}";
                    }
                    echo "<br>";
                    $updated++;
                } else {
                    echo "✗ Hata: {$page_data['title']} güncellenemedi<br>";
                    $errors++;
                }
            } else {
                echo "→ Sayfa zaten mevcut: {$page_data['title']} (ID: {$existing->ID})<br>";
                $skipped++;
            }
            continue;
        }
        
        // Yeni sayfa oluştur
        $post_data = array(
            'post_title'   => $page_data['title'],
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
            'post_parent'  => $parent_id,
        );
        
        $page_id = wp_insert_post($post_data);
        
        if ($page_id) {
            // Template varsa ata
            if (isset($page_data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
            
            echo "✓ Sayfa oluşturuldu: {$page_data['title']} (ID: {$page_id})";
            if ($parent_id) {
                echo " - Alt sayfa: {$page_data['parent']}";
            }
            echo "<br>";
            $imported++;
        } else {
            echo "✗ Hata: {$page_data['title']} oluşturulamadı<br>";
            $errors++;
        }
    }
    
    return array(
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors
    );
}

// Script çalıştır
echo "<h1>HTML Sayfaları WordPress'e İçe Aktarılıyor...</h1>";
echo "<p><strong>Mod:</strong> Mevcut sayfalar güncellenecek</p>";
echo "<hr>";

echo "<h2>1. Ana Sayfalar Oluşturuluyor...</h2>";
$parent_ids = create_parent_pages();
echo "<hr>";

echo "<h2>2. HTML Sayfaları İçe Aktarılıyor/Güncelleniyor...</h2>";
$stats = import_html_pages($pages, $html_dir, $parent_ids, true); // true = mevcut sayfaları güncelle
echo "<hr>";

echo "<h2>Özet</h2>";
echo "<ul>";
echo "<li><strong>Yeni oluşturulan:</strong> {$stats['imported']} sayfa</li>";
echo "<li><strong>Güncellenen:</strong> {$stats['updated']} sayfa</li>";
echo "<li><strong>Atlanan:</strong> {$stats['skipped']} sayfa</li>";
echo "<li><strong>Hata:</strong> {$stats['errors']} sayfa</li>";
echo "</ul>";

echo "<p><strong>İşlem tamamlandı!</strong> WordPress admin panelinde Sayfalar menüsünden içe aktarılan sayfaları görüntüleyebilirsiniz.</p>";
echo "<p><a href='/wp-admin/edit.php?post_type=page'>Sayfaları Görüntüle</a></p>";
