<?php
/**
 * Dernek Üye Listesi Import Scripti
 *
 * Kullanım (CLI):
 *   php tools/import_uyeler.php --dry-run                     (sütunları görmek için)
 *   php tools/import_uyeler.php                               (gerçek import)
 *   php tools/import_uyeler.php --file=liste.csv              (CSV dosyası)
 *   php tools/import_uyeler.php --file=liste.xls              (XLS dosyası)
 *   php tools/import_uyeler.php --skip=5                      (ilk 5 satırı atla)
 *   php tools/import_uyeler.php --sep=;                       (CSV ayraç karakteri)
 *
 * Desteklenen formatlar:
 *   - CSV UTF-8 (Excel: Farklı Kaydet → CSV UTF-8)
 *   - CSV Windows-1254 / Latin (TR Excel varsayılanı)
 *   - XLS (HTML tabanlı, Excel'in eski .xls formatı)
 */

if ( php_sapi_name() !== 'cli' ) {
    echo "Bu script sadece CLI'dan çalıştırılmalıdır.\n";
    exit(1);
}

// ── Argümanlar ────────────────────────────────────────────────────────────────
$dry_run    = in_array( '--dry-run', $argv );
$file_arg   = '';
$skip_rows  = 1; // varsayılan: ilk satır başlık
$csv_sep    = ',';

foreach ( $argv as $arg ) {
    if ( strpos( $arg, '--file=' ) === 0 ) {
        $file_arg = substr( $arg, 7 );
    }
    if ( strpos( $arg, '--skip=' ) === 0 ) {
        $skip_rows = max( 0, (int) substr( $arg, 7 ) );
    }
    if ( strpos( $arg, '--sep=' ) === 0 ) {
        $csv_sep = substr( $arg, 6 );
    }
}

$root      = dirname( __DIR__ );
// Varsayılan dosya: önce CSV dene, yoksa XLS
$default_file = file_exists( $root . '/tools/dernek-uye-listesi.csv' )
    ? $root . '/tools/dernek-uye-listesi.csv'
    : $root . '/tools/dernek-uye-listesi.xls';

$file_path = $file_arg
    ? ( file_exists( $file_arg ) ? $file_arg : $root . '/tools/' . $file_arg )
    : $default_file;

if ( ! file_exists( $file_path ) ) {
    echo "HATA: Dosya bulunamadı: {$file_path}\n";
    echo "Dosyayı tools/ klasörüne kopyalayın:\n";
    echo "  dernek-uye-listesi.csv  (önerilen)\n";
    echo "  dernek-uye-listesi.xls\n";
    exit(1);
}

$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
echo "Dosya: {$file_path} (format: {$ext})\n\n";

// ── WordPress yükle ──────────────────────────────────────────────────────────
require_once $root . '/wp-load.php';

// ── Dosyayı satır dizisine dönüştür ─────────────────────────────────────────
// $all_rows: [ [ 'kolon1', 'kolon2', ... ], ... ]
$all_rows = [];

if ( $ext === 'csv' ) {
    // ── CSV okuma ──────────────────────────────────────────────────────────
    $raw = file_get_contents( $file_path );

    // Encoding tespiti
    if ( strlen( $raw ) >= 2 && $raw[0] === "\xFF" && $raw[1] === "\xFE" ) {
        $raw = mb_convert_encoding( $raw, 'UTF-8', 'UTF-16LE' );
    } elseif ( strlen( $raw ) >= 2 && $raw[0] === "\xFE" && $raw[1] === "\xFF" ) {
        $raw = mb_convert_encoding( $raw, 'UTF-8', 'UTF-16BE' );
    } elseif ( ! mb_check_encoding( $raw, 'UTF-8' ) ) {
        // Windows TR Excel genellikle Windows-1254 kaydeder
        $raw = mb_convert_encoding( $raw, 'UTF-8', 'Windows-1254' );
    }
    $raw = ltrim( $raw, "\xEF\xBB\xBF" ); // UTF-8 BOM

    // Satır sonu normalleştir
    $raw = str_replace( "\r\n", "\n", $raw );
    $raw = str_replace( "\r", "\n", $raw );

    // Otomatik ayraç tespiti (virgül veya noktalı virgül)
    if ( $csv_sep === ',' ) {
        $first_line = strtok( $raw, "\n" );
        $comma_count     = substr_count( $first_line, ',' );
        $semicolon_count = substr_count( $first_line, ';' );
        if ( $semicolon_count > $comma_count ) {
            $csv_sep = ';';
            echo "Not: Ayraç otomatik olarak ';' seçildi.\n";
        }
    }

    foreach ( explode( "\n", $raw ) as $line ) {
        $line = rtrim( $line );
        if ( $line === '' ) continue;
        $all_rows[] = str_getcsv( $line, $csv_sep );
    }

} else {
    // ── XLS (HTML tabanlı) okuma ───────────────────────────────────────────
    $raw = file_get_contents( $file_path );

    if ( strlen( $raw ) >= 2 && $raw[0] === "\xFF" && $raw[1] === "\xFE" ) {
        $raw = mb_convert_encoding( $raw, 'UTF-8', 'UTF-16LE' );
    } elseif ( strlen( $raw ) >= 2 && $raw[0] === "\xFE" && $raw[1] === "\xFF" ) {
        $raw = mb_convert_encoding( $raw, 'UTF-8', 'UTF-16BE' );
    }
    $raw = ltrim( $raw, "\xEF\xBB\xBF" );

    $dom = new DOMDocument();
    libxml_use_internal_errors( true );
    $dom->loadHTML( '<?xml encoding="utf-8"?>' . $raw );
    libxml_clear_errors();

    $dom_rows = $dom->getElementsByTagName( 'tr' );
    if ( $dom_rows->length === 0 ) {
        echo "HATA: XLS dosyasında tablo satırı bulunamadı.\n";
        exit(1);
    }

    foreach ( $dom_rows as $tr ) {
        $cells = $tr->getElementsByTagName( 'th' );
        if ( $cells->length === 0 ) {
            $cells = $tr->getElementsByTagName( 'td' );
        }
        $row_data = [];
        foreach ( $cells as $cell ) {
            $row_data[] = trim( preg_replace( '/\s+/', ' ', $cell->textContent ) );
        }
        if ( ! empty( $row_data ) ) {
            $all_rows[] = $row_data;
        }
    }
}

if ( empty( $all_rows ) ) {
    echo "HATA: Dosyada veri satırı bulunamadı.\n";
    exit(1);
}

// ── Başlık satırını al ───────────────────────────────────────────────────────
$headers = array_map( function( $h ) {
    return trim( preg_replace( '/\s+/', ' ', $h ) );
}, $all_rows[0] );

echo "=== Dosyada bulunan sütunlar ===\n";
foreach ( $headers as $i => $h ) {
    echo "  [{$i}] {$h}\n";
}
echo "\n";

if ( $dry_run ) {
    echo "DRY-RUN modunda çalışıldı. Import için --dry-run parametresini kaldırın.\n";
    echo "\nÖrnek ilk 3 veri satırı:\n";
    for ( $i = 1; $i <= min( 3, count( $all_rows ) - 1 ); $i++ ) {
        echo "  Satır {$i}: " . implode( ' | ', $all_rows[ $i ] ) . "\n";
    }
    exit(0);
}

// ── Sütun Eşleştirme (Türkçe başlık → meta key) ──────────────────────────────
// Dosyanızdaki sütun adlarına göre düzenleyin.
// Anahtar: normalleştirilmiş başlık (küçük harf, trim), Değer: meta_key veya özel anahtar
$kolon_haritasi = [
    // Ad Soyad → post title
    'ad soyad'          => '__title__',
    'ad-soyad'          => '__title__',
    'adı soyadı'        => '__title__',
    'isim soyisim'      => '__title__',
    'isim'              => '__title__',
    'ad'                => '__title__',

    // Meta alanlar
    'e-posta'           => 'uye_email',
    'e-mail'            => 'uye_email',
    'eposta'            => 'uye_email',
    'email'             => 'uye_email',
    'mail'              => 'uye_email',

    'cep telefonu'      => 'uye_telefon',
    'telefon'           => 'uye_telefon',
    'gsm'               => 'uye_telefon',
    'cep'               => 'uye_telefon',
    'mobil'             => 'uye_telefon',

    'tc kimlik'         => 'uye_tc',
    'tc no'             => 'uye_tc',
    't.c. kimlik no'    => 'uye_tc',
    'tc kimlik no'      => 'uye_tc',
    'tc'                => 'uye_tc',

    'kayıt tarihi'      => 'uye_kayit_tarihi',
    'üyelik tarihi'     => 'uye_kayit_tarihi',
    'üye tarihi'        => 'uye_kayit_tarihi',
    'tarih'             => 'uye_kayit_tarihi',

    'adres'             => 'uye_adres',
    'ev adresi'         => 'uye_adres',

    'sabit tel'         => 'uye_sabit_tel',
    'sabit telefon'     => 'uye_sabit_tel',
    'ev telefonu'       => 'uye_sabit_tel',
    'iş telefonu'       => 'uye_sabit_tel',

    'faks'              => 'uye_faks',
    'fax'               => 'uye_faks',

    'iş adresi'         => 'uye_is_adres',
    'işyeri adresi'     => 'uye_is_adres',

    'çalıştığı kurum'   => 'uye_kurum',
    'kurum'             => 'uye_kurum',
    'hastane'           => 'uye_kurum',
    'iş yeri'           => 'uye_kurum',
    'çalıştığı yer'     => 'uye_kurum',
    'işyeri'            => 'uye_kurum',

    'derbis'            => 'uye_derbis',
    'derbis kaydı'      => 'uye_derbis',

    'üye no'            => '__uye_no__',
    'üyelik no'         => '__uye_no__',
    'üye numarası'      => '__uye_no__',

    'şehir'             => 'uye_sehir',
    'şehir/il'          => 'uye_sehir',
    'il'                => 'uye_sehir',
    'ilçe'              => 'uye_ilce',
];

// Başlıkları eşleştir
$kolon_map = [];
foreach ( $headers as $i => $h ) {
    $norm = mb_strtolower( trim( $h ), 'UTF-8' );
    if ( isset( $kolon_haritasi[ $norm ] ) ) {
        $kolon_map[ $i ] = $kolon_haritasi[ $norm ];
    } else {
        $kolon_map[ $i ] = null; // eşleşmedi, atlanacak
        echo "UYARI: Eşleşmeyen sütun [{$i}] \"{$h}\" → atlanıyor\n";
    }
}
echo "\n";

// ── İmport ───────────────────────────────────────────────────────────────────
$basarili  = 0;
$atlanan   = 0;
$guncellenen = 0;
$row_index = 0;

foreach ( $all_rows as $row_arr ) {
    $row_index++;
    if ( $row_index <= $skip_rows ) {
        continue; // başlık satırını atla
    }

    if ( empty( array_filter( $row_arr ) ) ) {
        continue; // tamamen boş satırı atla
    }

    // Hücre değerlerini topla
    $data = [];
    foreach ( $row_arr as $ci => $val ) {
        $data[ $ci ] = trim( preg_replace( '/\s+/', ' ', $val ) );
    }

    // Post title (Ad Soyad) bul
    $title = '';
    foreach ( $kolon_map as $ci => $meta_key ) {
        if ( $meta_key === '__title__' && isset( $data[ $ci ] ) ) {
            $title = sanitize_text_field( $data[ $ci ] );
            break;
        }
    }

    if ( empty( $title ) ) {
        // Sütun eşleşmesi yoksa ilk hücreyi ad soyad kabul et
        $title = sanitize_text_field( $data[0] ?? '' );
    }

    if ( empty( $title ) ) {
        echo "  Satır {$row_index}: Boş ad soyad, atlanıyor.\n";
        $atlanan++;
        continue;
    }

    // Aynı isimde üye var mı kontrol et
    $existing = get_posts( [
        'post_type'   => 'dernek_uye',
        'title'       => $title,
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ] );

    if ( ! empty( $existing ) ) {
        $post_id = $existing[0];
        echo "  Satır {$row_index}: \"{$title}\" zaten mevcut (ID: {$post_id}), güncelleniyor...\n";
        $guncellenen++;
    } else {
        // Yeni post oluştur
        $post_id = wp_insert_post( [
            'post_type'   => 'dernek_uye',
            'post_title'  => $title,
            'post_status' => 'publish',
            'post_author' => 1,
        ] );

        if ( is_wp_error( $post_id ) ) {
            echo "  Satır {$row_index}: HATA — {$post_id->get_error_message()}\n";
            $atlanan++;
            continue;
        }
        echo "  Satır {$row_index}: \"{$title}\" oluşturuldu (ID: {$post_id})\n";
        $basarili++;
    }

    // Meta verileri kaydet
    foreach ( $kolon_map as $ci => $meta_key ) {
        if ( $meta_key === null || $meta_key === '__title__' ) {
            continue;
        }

        $deger = isset( $data[ $ci ] ) ? sanitize_text_field( $data[ $ci ] ) : '';

        if ( $meta_key === '__uye_no__' ) {
            // Üye no dosyadan geliyorsa kaydet, ama sadece boşsa
            if ( ! empty( $deger ) && empty( get_post_meta( $post_id, 'uye_no', true ) ) ) {
                update_post_meta( $post_id, 'uye_no', $deger );
            }
            continue;
        }

        if ( $meta_key === 'uye_derbis' ) {
            // "evet"/"hayır" normalleştir
            $lower = mb_strtolower( $deger, 'UTF-8' );
            $deger = in_array( $lower, [ 'evet', 'var', 'yes', '1', 'true', 'x' ] ) ? 'evet' : 'hayır';
        }

        if ( $meta_key === 'uye_kayit_tarihi' ) {
            // Tarihi Y-m-d formatına çevir
            if ( ! empty( $deger ) ) {
                // dd.mm.yyyy veya dd/mm/yyyy desteği
                $deger = preg_replace( '/[\/\-]/', '.', $deger );
                $parts = explode( '.', $deger );
                if ( count( $parts ) === 3 ) {
                    if ( strlen( $parts[2] ) === 4 ) {
                        // dd.mm.yyyy
                        $deger = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                    }
                }
            }
        }

        if ( ! empty( $deger ) ) {
            update_post_meta( $post_id, $meta_key, $deger );
        }
    }
}

// ── Özet ─────────────────────────────────────────────────────────────────────
echo "\n=== Import Tamamlandı ===\n";
echo "  Yeni oluşturulan : {$basarili}\n";
echo "  Güncellenen      : {$guncellenen}\n";
echo "  Atlanan          : {$atlanan}\n";
echo "  Toplam işlenen   : " . ( $basarili + $guncellenen + $atlanan ) . "\n";
