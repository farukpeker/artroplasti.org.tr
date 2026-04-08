<?php
/**
 * Aidat Yönetimi
 *
 * - CPT: dernek_aidat  (her üye için bir kayıt; üye ID ile bağlantılı)
 * - Settings: Her yıl için aidat tutarı  (Ayarlar > Aidat Tutarları)
 * - Meta: 2012'den 2026'ya kadar her yıl ödeme durumu + tarih + notlar
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────
// Yardımcı: Aktif yıl listesi
// ─────────────────────────────────────────────
function artroplasti_aidat_yillari() {
    $baslangic = 2012;
    $bitis     = (int) date( 'Y' );
    $yillar    = array();
    for ( $y = $bitis; $y >= $baslangic; $y-- ) {
        $yillar[] = $y;
    }
    return $yillar;
}

// ─────────────────────────────────────────────
// 1. CPT Kaydı
// ─────────────────────────────────────────────
function artroplasti_register_aidat_post_type() {
    $labels = array(
        'name'               => 'Aidat Yönetimi',
        'singular_name'      => 'Aidat Kaydı',
        'menu_name'          => 'Aidat Yönetimi',
        'add_new'            => 'Yeni Kayıt',
        'add_new_item'       => 'Yeni Aidat Kaydı Ekle',
        'edit_item'          => 'Aidat Kaydını Düzenle',
        'search_items'       => 'Aidat Kaydı Ara',
        'not_found'          => 'Aidat kaydı bulunamadı',
    );

    $args = array(
        'labels'          => $labels,
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-money-alt',
        'menu_position'   => 6,
        'supports'        => array( 'title' ),  // başlık = üye adı/seçimi
        'has_archive'     => false,
        'show_in_rest'    => false,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
    );

    register_post_type( 'dernek_aidat', $args );
}
add_action( 'init', 'artroplasti_register_aidat_post_type' );

// ─────────────────────────────────────────────
// 2. Admin Liste Sütunları
// ─────────────────────────────────────────────
function artroplasti_aidat_columns( $columns ) {
    $yil_simdiki = (int) date( 'Y' );
    $new = array(
        'cb'           => $columns['cb'],
        'title'        => 'Ad Soyad',
        'aidat_email'  => 'E-posta',
        'aidat_tel'    => 'Telefon',
        'uye_id'       => 'Üye Kaydı',
    );
    // Son 4 yıl için sütun ekle
    for ( $y = $yil_simdiki; $y >= max( $yil_simdiki - 3, 2012 ); $y-- ) {
        $new[ 'aidat_' . $y ] = $y;
    }
    return $new;
}
add_filter( 'manage_dernek_aidat_posts_columns', 'artroplasti_aidat_columns' );

function artroplasti_aidat_columns_content( $column, $post_id ) {
    if ( $column === 'aidat_email' ) {
        echo esc_html( get_post_meta( $post_id, 'aidat_email', true ) );
        return;
    }
    if ( $column === 'aidat_tel' ) {
        echo esc_html( get_post_meta( $post_id, 'aidat_telefon', true ) );
        return;
    }
    if ( $column === 'uye_id' ) {
        $uye_id = (int) get_post_meta( $post_id, 'aidat_uye_id', true );
        if ( $uye_id ) {
            $link = get_edit_post_link( $uye_id );
            $name = get_the_title( $uye_id );
            echo '<a href="' . esc_url( $link ) . '">' . esc_html( $name ) . '</a>';
        } else {
            echo '<span style="color:#999;">Bağlı değil</span>';
        }
        return;
    }
    if ( strpos( $column, 'aidat_' ) === 0 ) {
        $yil    = str_replace( 'aidat_', '', $column );
        $durum  = get_post_meta( $post_id, 'aidat_durum_' . $yil, true );
        $tarih  = get_post_meta( $post_id, 'aidat_tarih_' . $yil, true );
        // Tutar
        $tutar  = get_option( 'aidat_tutar_' . $yil, '' );

        if ( $durum === 'odendi' ) {
            echo '<span style="color:green;font-weight:bold;">✓ Ödendi</span>';
            if ( $tarih ) {
                echo '<br><small>' . esc_html( $tarih ) . '</small>';
            }
        } elseif ( $durum === 'muaf' ) {
            echo '<span style="color:#888;">— Muaf</span>';
        } else {
            echo '<span style="color:#cc0000;">✗ Ödenmedi</span>';
            if ( $tutar ) {
                echo '<br><small>' . esc_html( $tutar ) . ' ₺</small>';
            }
        }
    }
}
add_action( 'manage_dernek_aidat_posts_custom_column', 'artroplasti_aidat_columns_content', 10, 2 );

// ─────────────────────────────────────────────
// 3. Meta Box
// ─────────────────────────────────────────────
function artroplasti_add_aidat_metabox() {
    add_meta_box(
        'artroplasti_aidat_uye_meta',
        'Üye Bilgileri',
        'artroplasti_render_aidat_uye_metabox',
        'dernek_aidat',
        'normal',
        'high'
    );
    add_meta_box(
        'artroplasti_aidat_odemeler_meta',
        'Aidat Ödemeleri (Yıllık)',
        'artroplasti_render_aidat_odemeler_metabox',
        'dernek_aidat',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'artroplasti_add_aidat_metabox' );

// ── Üye bilgileri meta box ──
function artroplasti_render_aidat_uye_metabox( $post ) {
    wp_nonce_field( 'artroplasti_aidat_meta_nonce', 'artroplasti_aidat_meta_nonce' );

    $email    = get_post_meta( $post->ID, 'aidat_email', true );
    $telefon  = get_post_meta( $post->ID, 'aidat_telefon', true );
    $uye_id   = (int) get_post_meta( $post->ID, 'aidat_uye_id', true );

    // Dernek üye listesini çek
    $uyeler = get_posts( array(
        'post_type'      => 'dernek_uye',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <style>
        .aidat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 24px; }
        .aidat-grid .full { grid-column: 1 / -1; }
        .aidat-grid label { display:block; font-weight:600; margin-bottom:3px; }
        .aidat-grid input, .aidat-grid select { width:100%; }
    </style>
    <div class="aidat-grid">
        <div class="full">
            <label>Üye Kaydına Bağla (İsteğe Bağlı)</label>
            <select name="aidat_uye_id">
                <option value="">— Üye seçin veya boş bırakın —</option>
                <?php foreach ( $uyeler as $uye ) : ?>
                    <option value="<?php echo esc_attr( $uye->ID ); ?>"
                        <?php selected( $uye_id, $uye->ID ); ?>>
                        <?php echo esc_html( $uye->post_title ); ?>
                        <?php
                        $uno = get_post_meta( $uye->ID, 'uye_no', true );
                        if ( $uno ) echo ' (' . esc_html( $uno ) . ')';
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">Üye kaydıyla eşleştirirseniz aidat paneli orada da görünür.</p>
        </div>
        <div>
            <label>E-posta</label>
            <input type="email" name="aidat_email"
                   value="<?php echo esc_attr( $email ); ?>" placeholder="ornek@email.com">
        </div>
        <div>
            <label>Telefon</label>
            <input type="tel" name="aidat_telefon"
                   value="<?php echo esc_attr( $telefon ); ?>" placeholder="05xx xxx xx xx">
        </div>
    </div>
    <?php
}

// ── Yıllık ödeme durumu meta box ──
function artroplasti_render_aidat_odemeler_metabox( $post ) {
    $yillar = artroplasti_aidat_yillari();
    ?>
    <style>
        .aidat-yil-table { width:100%; border-collapse:collapse; }
        .aidat-yil-table th { background:#f7f7f7; padding:8px 10px; text-align:left;
                               border-bottom:2px solid #ddd; font-size:13px; }
        .aidat-yil-table td { padding:8px 10px; border-bottom:1px solid #eee; vertical-align:middle; }
        .aidat-yil-table tr:hover td { background:#fafafa; }
        .aidat-yil-table select { min-width:120px; }
        .aidat-yil-table input[type=date] { min-width:140px; }
        .aidat-yil-table input[type=text] { width:180px; }
        .badge-odendi  { color:#1a7c1a; font-weight:700; }
        .badge-odenmedi { color:#cc0000; }
        .badge-muaf     { color:#777; }
        .tutar-bilgi   { color:#777; font-size:12px; margin-left:6px; }
    </style>
    <table class="aidat-yil-table">
        <thead>
            <tr>
                <th>Yıl</th>
                <th>Belirlenen Tutar</th>
                <th>Durum</th>
                <th>Ödeme Tarihi</th>
                <th>Notlar</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ( $yillar as $yil ) :
            $durum = get_post_meta( $post->ID, 'aidat_durum_' . $yil, true );
            $tarih = get_post_meta( $post->ID, 'aidat_tarih_' . $yil, true );
            $not   = get_post_meta( $post->ID, 'aidat_not_' . $yil, true );
            $tutar = get_option( 'aidat_tutar_' . $yil, '' );
            ?>
            <tr>
                <td><strong><?php echo esc_html( $yil ); ?></strong></td>
                <td>
                    <?php if ( $tutar ) : ?>
                        <strong><?php echo esc_html( number_format( (float) $tutar, 2, ',', '.' ) ); ?> ₺</strong>
                    <?php else : ?>
                        <span class="tutar-bilgi">Ayarlanmamış</span>
                    <?php endif; ?>
                </td>
                <td>
                    <select name="aidat_durum_<?php echo esc_attr( $yil ); ?>">
                        <option value=""        <?php selected( $durum, '' ); ?>>— Belirtilmedi —</option>
                        <option value="odendi"  <?php selected( $durum, 'odendi' ); ?>>✓ Ödendi</option>
                        <option value="odenmedi"<?php selected( $durum, 'odenmedi' ); ?>>✗ Ödenmedi</option>
                        <option value="muaf"    <?php selected( $durum, 'muaf' ); ?>>— Muaf —</option>
                    </select>
                </td>
                <td>
                    <input type="date" name="aidat_tarih_<?php echo esc_attr( $yil ); ?>"
                           value="<?php echo esc_attr( $tarih ); ?>">
                </td>
                <td>
                    <input type="text" name="aidat_not_<?php echo esc_attr( $yil ); ?>"
                           value="<?php echo esc_attr( $not ); ?>" placeholder="Açıklama...">
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

// ─────────────────────────────────────────────
// 4. Meta Kaydetme
// ─────────────────────────────────────────────
function artroplasti_save_aidat_meta( $post_id ) {
    if ( ! isset( $_POST['artroplasti_aidat_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['artroplasti_aidat_meta_nonce'], 'artroplasti_aidat_meta_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Üye bağlantısı ve temel alanlar
    if ( isset( $_POST['aidat_uye_id'] ) ) {
        update_post_meta( $post_id, 'aidat_uye_id', intval( $_POST['aidat_uye_id'] ) );
    }
    if ( isset( $_POST['aidat_email'] ) ) {
        update_post_meta( $post_id, 'aidat_email', sanitize_email( $_POST['aidat_email'] ) );
    }
    if ( isset( $_POST['aidat_telefon'] ) ) {
        update_post_meta( $post_id, 'aidat_telefon', sanitize_text_field( $_POST['aidat_telefon'] ) );
    }

    // Yıllık ödeme durumları
    $izin_durum = array( '', 'odendi', 'odenmedi', 'muaf' );
    foreach ( artroplasti_aidat_yillari() as $yil ) {
        $durum_key = 'aidat_durum_' . $yil;
        $tarih_key = 'aidat_tarih_' . $yil;
        $not_key   = 'aidat_not_'   . $yil;

        if ( isset( $_POST[ $durum_key ] ) ) {
            $durum = sanitize_text_field( $_POST[ $durum_key ] );
            if ( in_array( $durum, $izin_durum, true ) ) {
                update_post_meta( $post_id, $durum_key, $durum );
            }
        }
        if ( isset( $_POST[ $tarih_key ] ) ) {
            update_post_meta( $post_id, $tarih_key, sanitize_text_field( $_POST[ $tarih_key ] ) );
        }
        if ( isset( $_POST[ $not_key ] ) ) {
            update_post_meta( $post_id, $not_key, sanitize_text_field( $_POST[ $not_key ] ) );
        }
    }
}
add_action( 'save_post_dernek_aidat', 'artroplasti_save_aidat_meta' );

// ─────────────────────────────────────────────
// 5. Ayarlar Sayfası — Yıllık Aidat Tutarları
// ─────────────────────────────────────────────
function artroplasti_aidat_tutarlari_menu() {
    add_submenu_page(
        'edit.php?post_type=dernek_aidat',
        'Yıllık Aidat Tutarları',
        'Aidat Tutarları',
        'manage_options',
        'aidat-tutarlari',
        'artroplasti_aidat_tutarlari_page'
    );
}
add_action( 'admin_menu', 'artroplasti_aidat_tutarlari_menu' );

function artroplasti_aidat_tutarlari_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Kaydet
    if ( isset( $_POST['aidat_tutarlari_nonce'] )
         && wp_verify_nonce( $_POST['aidat_tutarlari_nonce'], 'aidat_tutarlari_save' ) ) {
        foreach ( artroplasti_aidat_yillari() as $yil ) {
            $key = 'aidat_tutar_' . $yil;
            if ( isset( $_POST[ $key ] ) ) {
                $tutar = floatval( str_replace( ',', '.', $_POST[ $key ] ) );
                update_option( $key, $tutar > 0 ? $tutar : '' );
            }
        }
        echo '<div class="updated notice"><p><strong>Aidat tutarları kaydedildi.</strong></p></div>';
    }

    $yillar = artroplasti_aidat_yillari();
    ?>
    <div class="wrap">
        <h1>Yıllık Aidat Tutarları</h1>
        <p>Her yıl için belirlenen aidat tutarını girin. Bu tutar aidat listesinde ve ödeme sayfasında kullanılır.</p>

        <form method="post">
            <?php wp_nonce_field( 'aidat_tutarlari_save', 'aidat_tutarlari_nonce' ); ?>
            <table class="wp-list-table widefat fixed striped" style="max-width:500px;">
                <thead>
                    <tr>
                        <th>Yıl</th>
                        <th>Aidat Tutarı (₺)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $yillar as $yil ) :
                        $tutar = get_option( 'aidat_tutar_' . $yil, '' );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $yil ); ?></strong></td>
                        <td>
                            <input type="number" name="aidat_tutar_<?php echo esc_attr( $yil ); ?>"
                                   value="<?php echo esc_attr( $tutar ); ?>"
                                   min="0" step="0.01" style="width:140px;"
                                   placeholder="0.00"> ₺
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Tutarları Kaydet">
            </p>
        </form>
    </div>
    <?php
}

// ─────────────────────────────────────────────
// 6. Üye sayfasında aidat özeti (üye meta box'ına ek bilgi)
// ─────────────────────────────────────────────
function artroplasti_add_aidat_ozet_metabox() {
    add_meta_box(
        'artroplasti_aidat_ozet',
        'Aidat Özeti',
        'artroplasti_render_aidat_ozet_metabox',
        'dernek_uye',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'artroplasti_add_aidat_ozet_metabox' );

function artroplasti_render_aidat_ozet_metabox( $post ) {
    // Bu üyeye bağlı aidat kaydını bul
    $aidat_posts = get_posts( array(
        'post_type'      => 'dernek_aidat',
        'posts_per_page' => 1,
        'meta_key'       => 'aidat_uye_id',
        'meta_value'     => $post->ID,
        'post_status'    => 'publish',
    ) );

    if ( empty( $aidat_posts ) ) {
        echo '<p>Bağlı aidat kaydı yok. '
           . '<a href="' . esc_url( admin_url( 'post-new.php?post_type=dernek_aidat' ) ) . '">Yeni aidat kaydı ekle</a>.</p>';
        return;
    }

    $aidat_id = $aidat_posts[0]->ID;
    $yillar   = artroplasti_aidat_yillari();
    echo '<table style="width:100%;font-size:12px;">';
    echo '<tr><th style="text-align:left;">Yıl</th><th>Durum</th></tr>';
    foreach ( array_slice( $yillar, 0, 5 ) as $yil ) {
        $durum = get_post_meta( $aidat_id, 'aidat_durum_' . $yil, true );
        $label = '';
        $style = '';
        if ( $durum === 'odendi' ) {
            $label = '✓ Ödendi';
            $style = 'color:green;font-weight:bold;';
        } elseif ( $durum === 'muaf' ) {
            $label = '— Muaf';
            $style = 'color:#888;';
        } elseif ( $durum === 'odenmedi' ) {
            $label = '✗ Ödenmedi';
            $style = 'color:#c00;';
        } else {
            $label = '—';
            $style = 'color:#ccc;';
        }
        echo '<tr><td>' . esc_html( $yil ) . '</td>'
           . '<td style="' . esc_attr( $style ) . '">' . esc_html( $label ) . '</td></tr>';
    }
    echo '</table>';
    echo '<p><a href="' . esc_url( get_edit_post_link( $aidat_id ) ) . '">Tüm aidat kaydını düzenle →</a></p>';
}
