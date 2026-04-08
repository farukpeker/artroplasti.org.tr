<?php
/**
 * Dernek Üye JSON Import Scripti
 *
 * Kullanım (CLI):
 *   php tools/import_uyeler_json.php --dry-run
 *   php tools/import_uyeler_json.php
 *   php tools/import_uyeler_json.php --file=c:\Users\Peker\Downloads\dernek_uye_listesi.json
 */

if ( php_sapi_name() !== 'cli' ) {
    echo "Bu script sadece CLI'dan çalıştırılmalıdır.\n";
    exit(1);
}

// ── Argümanlar ────────────────────────────────────────────────────────────────
$dry_run  = in_array( '--dry-run', $argv );
$file_arg = '';

foreach ( $argv as $arg ) {
    if ( strpos( $arg, '--file=' ) === 0 ) {
        $file_arg = substr( $arg, 7 );
    }
}

$root = dirname( __DIR__ );

$default_paths = [
    $root . '/tools/dernek_uye_listesi.json',
    $root . '/tools/dernek-uye-listesi.json',
    'c:/Users/Peker/Downloads/dernek_uye_listesi.json',
    'c:/Users/Peker/Downloads/dernek-uye-listesi.json',
];

if ( $file_arg ) {
    $file_path = file_exists( $file_arg ) ? $file_arg : $root . '/tools/' . $file_arg;
} else {
    $file_path = '';
    foreach ( $default_paths as $p ) {
        if ( file_exists( $p ) ) {
            $file_path = $p;
            break;
        }
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
$raw  = file_get_contents( $file_path );
// UTF-8 BOM temizle
$raw  = ltrim( $raw, "\xEF\xBB\xBF" );
$uyeler = json_decode( $raw, true );

if ( ! is_array( $uyeler ) || empty( $uyeler ) ) {
    echo "HATA: JSON okunamadı veya boş — " . json_last_error_msg() . "\n";
    exit(1);
}

echo "Toplam kayıt: " . count( $uyeler ) . "\n\n";

// ── Yardımcı: adres alanını ayrıştır ─────────────────────────────────────────
function parse_adres_field( ?string $adres ): array {
    $result = [
        'sabit_tel' => '',
        'faks'      => '',
        'adres'     => '',
        'is_adres'  => '',
    ];

    if ( empty( $adres ) ) {
        return $result;
    }

    // Her satırı işle
    $lines = preg_split( '/\n|\r/', $adres );
    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( empty( $line ) ) continue;

        // [SABİT TELEFON] — ilkini al
        if ( preg_match( '/\[SABİT TELEFON\]\s*\S.*?\s+([\d\s\/\-\+\.]+)/u', $line, $m ) ) {
            if ( empty( $result['sabit_tel'] ) ) {
                $result['sabit_tel'] = trim( $m[1] );
            }
            continue;
        }

        // [FAX]
        if ( preg_match( '/\[FAX\]\s*\S*\s*([\d\s\/\-\+\.]+)/u', $line, $m ) ) {
            $result['faks'] = trim( $m[1] );
            continue;
        }

        // [ADRES]İş Adresi
        if ( preg_match( '/\[ADRES\]\s*İş Adresi\s+(.+)/u', $line, $m ) ) {
            if ( empty( $result['is_adres'] ) ) {
                $result['is_adres'] = trim( $m[1] );
            }
            continue;
        }

        // [ADRES]Yazışma Adresi
        if ( preg_match( '/\[ADRES\]\s*Yazışma Adresi\s+(.+)/u', $line, $m ) ) {
            if ( empty( $result['adres'] ) ) {
                $result['adres'] = trim( $m[1] );
            }
            continue;
        }

        // [ADRES]Ev Adresi — yazışma yoksa kullan
        if ( preg_match( '/\[ADRES\]\s*Ev Adresi\s+(.+)/u', $line, $m ) ) {
            if ( empty( $result['adres'] ) ) {
                $result['adres'] = trim( $m[1] );
            }
            continue;
        }

        // [ADRES]Muayenehane Adresi — is_adres yoksa kullan
        if ( preg_match( '/\[ADRES\]\s*Muayenehane Adresi\s+(.+)/u', $line, $m ) ) {
            if ( empty( $result['is_adres'] ) ) {
                $result['is_adres'] = trim( $m[1] );
            }
            continue;
        }
    }

    return $result;
}

// ── Yardımcı: telefon formatla ────────────────────────────────────────────────
function format_telefon( ?string $tel ): string {
    if ( empty( $tel ) ) return '';
    // "90 5322415693" → "05322415693"
    $tel = trim( $tel );
    $tel = preg_replace( '/\s+/', '', $tel );
    if ( strpos( $tel, '90' ) === 0 && strlen( $tel ) === 12 ) {
        $tel = '0' . substr( $tel, 2 );
    }
    return $tel;
}

// ── Yardımcı: belgeler ayrıştır ───────────────────────────────────────────────
function parse_belgeler( ?string $belgeler_str ): array {
    if ( empty( $belgeler_str ) ) return [];

    // Anahtar kelime bazlı eşleştirme (encoding güvenli)
    $sonuc = [];

    $kontrol = [
        'basvuru_formu'    => 'ba',   // başvuru
        'nufus_cuzdani'    => 'f',  // nüfus
        'fotograf'         => 'foto', // fotoğraf
        'uzmanlik_belgesi' => 'uzman', // uzmanlık
        'referans_imzasi'  => 'referans', // referans
    ];

    $parcalar = explode( '|', $belgeler_str );
    foreach ( $parcalar as $parca ) {
        $parca = trim( $parca );
        // ": Evet" veya ":Evet" içermeli
        if ( stripos( $parca, 'Evet' ) === false ) continue;

        foreach ( $kontrol as $meta_key => $ipucu ) {
            if ( stripos( $parca, $ipucu ) !== false && ! in_array( $meta_key, $sonuc ) ) {
                $sonuc[] = $meta_key;
                break;
            }
        }
    }

    return $sonuc;
}

// ── Yardımcı: kurum çıkar ─────────────────────────────────────────────────────
function parse_kurum( ?string $profil ): string {
    if ( empty( $profil ) ) return '';
    if ( preg_match( '/Çalıştı[gğ][ıi] Kurum\s*:\s*(.+)/iu', $profil, $m ) ) {
        return trim( $m[1] );
    }
    return trim( $profil );
}

// ── İmport ───────────────────────────────────────────────────────────────────
$basarili    = 0;
$guncellenen = 0;
$atlanan     = 0;

foreach ( $uyeler as $i => $uye ) {
    $satir = $i + 1;

    $ad_soyad = sanitize_text_field( $uye['adiSoyadi'] ?? '' );
    if ( empty( $ad_soyad ) ) {
        echo "  [{$satir}] Boş ad soyad — atlanıyor.\n";
        $atlanan++;
        continue;
    }

    // Ayrıştırmalar
    $adres_data = parse_adres_field( $uye['adres'] ?? null );
    $telefon    = format_telefon( $uye['telefon'] ?? '' );
    $belgeler   = parse_belgeler( $uye['belgeler'] ?? null );
    $kurum      = parse_kurum( $uye['uyeProfil'] ?? null );
    $derbis     = ( stripos( $uye['derbisKaydi'] ?? '', 'evet' ) !== false ) ? 'evet' : 'hayır';

    if ( $dry_run ) {
        echo "  [{$satir}] {$ad_soyad}\n";
        echo "         telefon     : {$telefon}\n";
        echo "         email       : " . ( $uye['email'] ?? '' ) . "\n";
        echo "         tc          : " . ( $uye['tcKimlik'] ?? '' ) . "\n";
        echo "         kayit_tarihi: " . ( $uye['kayitTarihi'] ?? '' ) . "\n";
        echo "         sabit_tel   : {$adres_data['sabit_tel']}\n";
        echo "         faks        : {$adres_data['faks']}\n";
        echo "         adres       : {$adres_data['adres']}\n";
        echo "         is_adres    : {$adres_data['is_adres']}\n";
        echo "         kurum       : {$kurum}\n";
        echo "         derbis      : {$derbis}\n";
        echo "         belgeler    : " . implode( ', ', $belgeler ) . "\n";
        echo "\n";
        continue;
    }

    // Aynı isimde üye var mı?
    $existing = get_posts( [
        'post_type'   => 'dernek_uye',
        'title'       => $ad_soyad,
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ] );

    if ( ! empty( $existing ) ) {
        $post_id = $existing[0];
        echo "  [{$satir}] \"{$ad_soyad}\" mevcut (ID: {$post_id}) — güncelleniyor.\n";
        $guncellenen++;
    } else {
        $post_id = wp_insert_post( [
            'post_type'   => 'dernek_uye',
            'post_title'  => $ad_soyad,
            'post_status' => 'publish',
            'post_author' => 1,
        ] );

        if ( is_wp_error( $post_id ) ) {
            echo "  [{$satir}] HATA: {$post_id->get_error_message()}\n";
            $atlanan++;
            continue;
        }
        echo "  [{$satir}] \"{$ad_soyad}\" oluşturuldu (ID: {$post_id})\n";
        $basarili++;
    }

    // Üye no — JSON'dan geliyorsa ve henüz kapılanmamışsa ata
    $uyeNo_json = $uye['uyeNo'] ?? null;
    if ( ! empty( $uyeNo_json ) && empty( get_post_meta( $post_id, 'uye_no', true ) ) ) {
        update_post_meta( $post_id, 'uye_no', sanitize_text_field( (string) $uyeNo_json ) );
        update_post_meta( $post_id, 'uye_sira', (int) $uyeNo_json );
    }

    // Text meta alanları
    $meta_map = [
        'uye_email'        => sanitize_email( $uye['email'] ?? '' ),
        'uye_telefon'      => $telefon,
        'uye_tc'           => sanitize_text_field( $uye['tcKimlik'] ?? '' ),
        'uye_kayit_tarihi' => sanitize_text_field( $uye['kayitTarihi'] ?? '' ),
        'uye_adres'        => sanitize_textarea_field( $adres_data['adres'] ),
        'uye_sabit_tel'    => sanitize_text_field( $adres_data['sabit_tel'] ),
        'uye_faks'         => sanitize_text_field( $adres_data['faks'] ),
        'uye_is_adres'     => sanitize_textarea_field( $adres_data['is_adres'] ),
        'uye_kurum'        => sanitize_text_field( $kurum ),
        'uye_derbis'       => $derbis,
    ];

    foreach ( $meta_map as $key => $value ) {
        if ( $value !== '' ) {
            update_post_meta( $post_id, $key, $value );
        }
    }

    // Belgeler (dizi)
    if ( ! empty( $belgeler ) ) {
        update_post_meta( $post_id, 'uye_belgeler', $belgeler );
    }
}

// ── Özet ─────────────────────────────────────────────────────────────────────
if ( $dry_run ) {
    echo "=== DRY-RUN tamamlandı. Gerçek import için --dry-run'ı kaldırın. ===\n";
} else {
    echo "\n=== Import Tamamlandı ===\n";
    echo "  Yeni oluşturulan : {$basarili}\n";
    echo "  Güncellenen      : {$guncellenen}\n";
    echo "  Atlanan          : {$atlanan}\n";
    echo "  Toplam           : " . ( $basarili + $guncellenen + $atlanan ) . "\n";
}
