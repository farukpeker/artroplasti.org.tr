<?php
/**
 * Aidat JSON Import Scripti
 *
 * - Her üyeyi e-posta veya ad soyad ile dernek_uye CPT'sinde arar
 * - dernek_aidat kaydı yoksa oluşturur, varsa günceller
 * - Her yıl için aidat_durum_{yil} ve aidat_tutar_{yil} (wp_options) kaydeder
 *
 * Kullanım (CLI):
 *   php tools/import_aidat_json.php --dry-run
 *   php tools/import_aidat_json.php
 *   php tools/import_aidat_json.php --file=c:\path\aidat.json
 */

if ( php_sapi_name() !== 'cli' ) {
    echo "Bu script sadece CLI'dan çalıştırılmalıdır.\n";
    exit(1);
}

// ── Argümanlar ───────────────────────────────────────────────────────────────
$dry_run  = in_array( '--dry-run', $argv );
$file_arg = '';
foreach ( $argv as $arg ) {
    if ( strpos( $arg, '--file=' ) === 0 ) {
        $file_arg = substr( $arg, 7 );
    }
}

$root = dirname( __DIR__ );

$default_paths = [
    $root . '/tools/aidat_bilgileri.json',
    $root . '/tools/aidat-bilgileri.json',
    'c:/Users/Peker/Downloads/aidat_bilgileri.json',
    'c:/Users/Peker/Downloads/aidat-bilgileri.json',
];

if ( $file_arg ) {
    $file_path = file_exists( $file_arg ) ? $file_arg : $root . '/tools/' . $file_arg;
} else {
    $file_path = '';
    foreach ( $default_paths as $p ) {
        if ( file_exists( $p ) ) { $file_path = $p; break; }
    }
}

if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
    echo "HATA: JSON dosyası bulunamadı.\n";
    echo "Dosyayı tools/ klasörüne kopyalayın veya --file= ile tam yol belirtin.\n";
    exit(1);
}

echo "Dosya: {$file_path}\n\n";

// ── WordPress yükle ──────────────────────────────────────────────────────────
require_once $root . '/wp-load.php';

// ── JSON oku ─────────────────────────────────────────────────────────────────
$raw    = file_get_contents( $file_path );
$raw    = ltrim( $raw, "\xEF\xBB\xBF" );
$kayitlar = json_decode( $raw, true );

if ( ! is_array( $kayitlar ) || empty( $kayitlar ) ) {
    echo "HATA: JSON okunamadı — " . json_last_error_msg() . "\n";
    exit(1);
}

echo "Toplam kayıt: " . count( $kayitlar ) . "\n\n";

// ── Üye arama: önce e-posta meta, sonra başlık ───────────────────────────────
function bul_uye_id( string $email, string $ad_soyad ): int {
    global $wpdb;

    // 1. e-posta ile meta arama
    if ( ! empty( $email ) ) {
        $uye_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta}
             WHERE meta_key = 'uye_email' AND meta_value = %s
             LIMIT 1",
            $email
        ) );
        if ( $uye_id ) return (int) $uye_id;
    }

    // 2. ad soyad ile başlık eşleştirme (büyük-küçük harf duyarsız)
    if ( ! empty( $ad_soyad ) ) {
        $uye_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts}
             WHERE post_type = 'dernek_uye'
               AND post_status != 'trash'
               AND LOWER(post_title) = LOWER(%s)
             LIMIT 1",
            $ad_soyad
        ) );
        if ( $uye_id ) return (int) $uye_id;
    }

    return 0;
}

// ── Mevcut aidat kaydını bul (uye_id'ye göre) ────────────────────────────────
function bul_aidat_post_id( int $uye_id ): int {
    global $wpdb;
    if ( ! $uye_id ) return 0;
    $id = $wpdb->get_var( $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
         WHERE meta_key = 'aidat_uye_id' AND meta_value = %d
         LIMIT 1",
        $uye_id
    ) );
    return $id ? (int) $id : 0;
}

// ── İmport ───────────────────────────────────────────────────────────────────
$basarili    = 0;
$guncellenen = 0;
$bulunmayan  = 0;
$atlanan     = 0;

foreach ( $kayitlar as $i => $kayit ) {
    $satir     = $i + 1;
    $ad_soyad  = trim( $kayit['adiSoyadi'] ?? '' );
    $email     = trim( $kayit['email']     ?? '' );
    $telefon   = trim( $kayit['telefon']   ?? '' );
    $aidatlar  = $kayit['aidatlar']        ?? [];

    if ( empty( $ad_soyad ) ) {
        echo "  [{$satir}] Boş ad soyad — atlanıyor.\n";
        $atlanan++;
        continue;
    }

    // Üyeyi bul
    $uye_id = bul_uye_id( $email, $ad_soyad );

    if ( $dry_run ) {
        $bulunan = $uye_id ? "✓ Üye ID: {$uye_id}" : "✗ Üye bulunamadı";
        echo "  [{$satir}] {$ad_soyad} | {$email} → {$bulunan}\n";
        foreach ( $aidatlar as $a ) {
            $d = $a['durum'] === 'ODENDI' ? 'odendi' : 'odenmedi';
            echo "         {$a['yil']}: {$d} ({$a['tutar']} TL)\n";
        }
        echo "\n";
        continue;
    }

    // Aidat kaydı oluştur veya güncelle
    $aidat_post_id = $uye_id ? bul_aidat_post_id( $uye_id ) : 0;

    // Başlık: üye adından al, yoksa JSON'dan
    $baslik = $ad_soyad;
    if ( $uye_id ) {
        $uye_post = get_post( $uye_id );
        if ( $uye_post ) $baslik = $uye_post->post_title;
    }

    if ( $aidat_post_id ) {
        // Güncelle — yalnızca başlığı sync et
        wp_update_post( [ 'ID' => $aidat_post_id, 'post_title' => $baslik ] );
        echo "  [{$satir}] \"{$ad_soyad}\" — aidat kaydı güncellendi (ID: {$aidat_post_id}";
        echo $uye_id ? ", Üye: {$uye_id}" : ", Üye eşleşmedi";
        echo ")\n";
        $guncellenen++;
    } else {
        $aidat_post_id = wp_insert_post( [
            'post_type'   => 'dernek_aidat',
            'post_title'  => $baslik,
            'post_status' => 'publish',
            'post_author' => 1,
        ] );

        if ( is_wp_error( $aidat_post_id ) ) {
            echo "  [{$satir}] HATA: {$aidat_post_id->get_error_message()}\n";
            $atlanan++;
            continue;
        }
        echo "  [{$satir}] \"{$ad_soyad}\" — aidat kaydı oluşturuldu (ID: {$aidat_post_id}";
        echo $uye_id ? ", Üye: {$uye_id}" : ", Üye eşleşmedi";
        echo ")\n";
        $basarili++;

        if ( ! $uye_id ) $bulunmayan++;
    }

    // Üye bağlantısı + iletişim
    if ( $uye_id ) {
        update_post_meta( $aidat_post_id, 'aidat_uye_id', $uye_id );
    }
    if ( ! empty( $email ) ) {
        update_post_meta( $aidat_post_id, 'aidat_email', sanitize_email( $email ) );
    }
    if ( ! empty( $telefon ) ) {
        // "5321234567" → "05321234567"
        $tel = preg_replace( '/\s+/', '', $telefon );
        if ( strlen( $tel ) === 10 && $tel[0] !== '0' ) $tel = '0' . $tel;
        update_post_meta( $aidat_post_id, 'aidat_telefon', sanitize_text_field( $tel ) );
    }

    // Her yıl için aidat durumu
    foreach ( $aidatlar as $a ) {
        $yil   = (int) ( $a['yil']   ?? 0 );
        $tutar = (float) ( $a['tutar'] ?? 0 );
        $durum = ( isset( $a['durum'] ) && $a['durum'] === 'ODENDI' ) ? 'odendi' : 'odenmedi';

        if ( $yil < 2012 || $yil > 2030 ) continue;

        update_post_meta( $aidat_post_id, 'aidat_durum_' . $yil, $durum );

        // Yıllık tutar global ayarı — henüz set edilmemişse kaydet
        if ( $tutar > 0 && ! get_option( 'aidat_tutar_' . $yil ) ) {
            update_option( 'aidat_tutar_' . $yil, $tutar );
        }
    }
}

// ── Özet ─────────────────────────────────────────────────────────────────────
if ( $dry_run ) {
    echo "=== DRY-RUN tamamlandı. Gerçek import için --dry-run'ı kaldırın. ===\n";
} else {
    echo "\n=== Import Tamamlandı ===\n";
    echo "  Yeni oluşturulan       : {$basarili}\n";
    echo "  Güncellenen            : {$guncellenen}\n";
    echo "  Üye eşleşmeyen kayıt  : {$bulunmayan}\n";
    echo "  Atlanan                : {$atlanan}\n";
    echo "  Toplam                 : " . ( $basarili + $guncellenen + $atlanan ) . "\n";
}
