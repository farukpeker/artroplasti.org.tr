<?php
/**
 * Dernek Üye Yönetimi
 * Custom Post Type: dernek_uye
 * Alanlar: Üyelik No, Ad Soyad, E-posta, Telefon, TC Kimlik,
 *          Kayıt Tarihi, Adres, Sabit Tel, Faks, İş Adresi,
 *          Çalıştığı Kurum, Derbis Kaydı, Üye Belgeleri (checkbox seti)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────
// 1. CPT Kaydı
// ─────────────────────────────────────────────
function artroplasti_register_uye_post_type() {
    $labels = array(
        'name'               => 'Dernek Üyeleri',
        'singular_name'      => 'Üye',
        'menu_name'          => 'Üye Yönetimi',
        'add_new'            => 'Yeni Üye',
        'add_new_item'       => 'Yeni Üye Ekle',
        'edit_item'          => 'Üyeyi Düzenle',
        'view_item'          => 'Üyeyi Görüntüle',
        'search_items'       => 'Üye Ara',
        'not_found'          => 'Üye bulunamadı',
        'not_found_in_trash' => 'Çöp kutusunda üye bulunamadı',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,       // Ön yüzde görünmesin
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-id',
        'menu_position'      => 5,
        'supports'           => array( 'title' ), // Başlık = Ad Soyad
        'has_archive'        => false,
        'show_in_rest'       => false,
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );

    register_post_type( 'dernek_uye', $args );
}
add_action( 'init', 'artroplasti_register_uye_post_type' );

// ─────────────────────────────────────────────
// 2. Otomatik Üyelik Numarası
// ─────────────────────────────────────────────
function artroplasti_generate_uye_no( $post_id ) {
    $existing = get_post_meta( $post_id, 'uye_no', true );
    if ( ! empty( $existing ) ) {
        return; // zaten atanmış
    }

    // En büyük numaralı üyeyi bul
    $args = array(
        'post_type'      => 'dernek_uye',
        'posts_per_page' => 1,
        'post_status'    => 'any',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'meta_key'       => 'uye_sira',
        'fields'         => 'ids',
    );
    $query   = get_posts( $args );
    $last_no = 0;
    if ( ! empty( $query ) ) {
        $last_no = (int) get_post_meta( $query[0], 'uye_sira', true );
    }

    $yeni_sira = $last_no + 1;
    $yil       = date( 'Y' );
    $uye_no    = $yil . '-' . str_pad( $yeni_sira, 3, '0', STR_PAD_LEFT );

    update_post_meta( $post_id, 'uye_sira', $yeni_sira );
    update_post_meta( $post_id, 'uye_no', sanitize_text_field( $uye_no ) );
}
add_action( 'wp_insert_post', 'artroplasti_generate_uye_no', 10, 1 );

// ─────────────────────────────────────────────
// 3. Admin Liste Sütunları
// ─────────────────────────────────────────────
function artroplasti_uye_columns( $columns ) {
    $new = array();
    $new['cb']          = $columns['cb'];
    $new['uye_no']      = 'Üye No';
    $new['title']       = 'Ad Soyad';
    $new['uye_email']   = 'E-posta';
    $new['uye_tel']     = 'Telefon';
    $new['uye_kurum']   = 'Kurum';
    $new['uye_derbis']  = 'Derbis';
    $new['kayit_tarihi'] = 'Kayıt Tarihi';
    return $new;
}
add_filter( 'manage_dernek_uye_posts_columns', 'artroplasti_uye_columns' );

function artroplasti_uye_columns_content( $column, $post_id ) {
    switch ( $column ) {
        case 'uye_no':
            echo esc_html( get_post_meta( $post_id, 'uye_no', true ) );
            break;
        case 'uye_email':
            echo esc_html( get_post_meta( $post_id, 'uye_email', true ) );
            break;
        case 'uye_tel':
            echo esc_html( get_post_meta( $post_id, 'uye_telefon', true ) );
            break;
        case 'uye_kurum':
            echo esc_html( get_post_meta( $post_id, 'uye_kurum', true ) );
            break;
        case 'uye_derbis':
            $derbis = get_post_meta( $post_id, 'uye_derbis', true );
            echo $derbis === 'evet'
                ? '<span style="color:green;font-weight:bold;">✓ Evet</span>'
                : '<span style="color:#999;">✗ Hayır</span>';
            break;
        case 'kayit_tarihi':
            echo esc_html( get_post_meta( $post_id, 'uye_kayit_tarihi', true ) );
            break;
    }
}
add_action( 'manage_dernek_uye_posts_custom_column', 'artroplasti_uye_columns_content', 10, 2 );

// Üye no sütununa göre sıralama
function artroplasti_uye_sortable_columns( $columns ) {
    $columns['uye_no'] = 'uye_no';
    return $columns;
}
add_filter( 'manage_edit-dernek_uye_sortable_columns', 'artroplasti_uye_sortable_columns' );

// Arama filtresi: e-posta ve TC'ye de bak
function artroplasti_uye_search_filter( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( $query->get( 'post_type' ) !== 'dernek_uye' ) {
        return;
    }
    $search = $query->get( 's' );
    if ( empty( $search ) ) {
        return;
    }
    // Meta alanlarda da ara
    add_filter( 'posts_join', 'artroplasti_uye_search_join' );
    add_filter( 'posts_where', 'artroplasti_uye_search_where' );
    add_filter( 'posts_distinct', 'artroplasti_uye_search_distinct' );
}
add_action( 'pre_get_posts', 'artroplasti_uye_search_filter' );

function artroplasti_uye_search_join( $join ) {
    global $wpdb, $wp_query;
    $search = $wp_query->get( 's' );
    if ( ! empty( $search ) ) {
        $join .= " LEFT JOIN {$wpdb->postmeta} pm_search ON ({$wpdb->posts}.ID = pm_search.post_id)";
    }
    return $join;
}

function artroplasti_uye_search_where( $where ) {
    global $wpdb, $wp_query;
    $search = $wp_query->get( 's' );
    if ( ! empty( $search ) ) {
        $like  = '%' . $wpdb->esc_like( $search ) . '%';
        $where = preg_replace(
            "/\(\s*{$wpdb->posts}.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "({$wpdb->posts}.post_title LIKE $1) OR (pm_search.meta_value LIKE '" . esc_sql( $like ) . "')",
            $where
        );
    }
    return $where;
}

function artroplasti_uye_search_distinct( $distinct ) {
    return 'DISTINCT';
}

// ─────────────────────────────────────────────
// 4. Meta Box
// ─────────────────────────────────────────────
function artroplasti_add_uye_metabox() {
    add_meta_box(
        'artroplasti_uye_meta',
        'Üye Bilgileri',
        'artroplasti_render_uye_metabox',
        'dernek_uye',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'artroplasti_add_uye_metabox' );

function artroplasti_render_uye_metabox( $post ) {
    wp_nonce_field( 'artroplasti_uye_meta_nonce', 'artroplasti_uye_meta_nonce' );

    $uye_no        = get_post_meta( $post->ID, 'uye_no', true );
    $email         = get_post_meta( $post->ID, 'uye_email', true );
    $telefon       = get_post_meta( $post->ID, 'uye_telefon', true );
    $tc            = get_post_meta( $post->ID, 'uye_tc', true );
    $kayit_tarihi  = get_post_meta( $post->ID, 'uye_kayit_tarihi', true );
    $adres         = get_post_meta( $post->ID, 'uye_adres', true );
    $sabit_tel     = get_post_meta( $post->ID, 'uye_sabit_tel', true );
    $faks          = get_post_meta( $post->ID, 'uye_faks', true );
    $is_adres      = get_post_meta( $post->ID, 'uye_is_adres', true );
    $kurum         = get_post_meta( $post->ID, 'uye_kurum', true );
    $derbis        = get_post_meta( $post->ID, 'uye_derbis', true );
    $belgeler      = get_post_meta( $post->ID, 'uye_belgeler', true );
    if ( ! is_array( $belgeler ) ) {
        $belgeler = array();
    }

    $belge_options = array(
        'basvuru_formu'    => 'Üye Başvuru Formu',
        'nufus_cuzdani'    => 'Nüfus Cüzdanı',
        'fotograf'         => 'Fotoğraf',
        'uzmanlik_belgesi' => 'Uzmanlık Belgesi',
        'referans_imzasi'  => 'Referans İmzası',
    );
    ?>
    <style>
        .uye-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; margin-bottom: 20px; }
        .uye-meta-grid .full { grid-column: 1 / -1; }
        .uye-meta-grid label { display: block; font-weight: 600; margin-bottom: 4px; }
        .uye-meta-grid input:not([type="checkbox"]), .uye-meta-grid textarea, .uye-meta-grid select { width: 100%; }
        .uye-belgeler-list { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 6px; }
        .uye-belgeler-list label { font-weight: normal; display: flex; align-items: center; gap: 6px; cursor: pointer; }
        .uye-belgeler-list input[type="checkbox"] {
            -webkit-appearance: checkbox !important;
            appearance: checkbox !important;
            width: 16px !important;
            height: 16px !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 1px solid #8c8f94 !important;
            background: #fff !important;
            box-shadow: none !important;
            cursor: pointer;
        }
        .uye-belgeler-list input[type="checkbox"]:checked {
            background-color: #2271b1 !important;
            border-color: #2271b1 !important;
        }
        .uye-section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
                             color: #555; border-bottom: 1px solid #ddd; margin: 16px 0 12px; padding-bottom: 4px; }
        .uye-no-display { font-size: 18px; font-weight: 700; color: #B81838; padding: 8px 12px;
                          background: #fff0f3; border: 1px solid #f3d9df; border-radius: 6px; display: inline-block; }
    </style>

    <div class="uye-meta-grid">

        <div>
            <p class="uye-section-title">Üyelik Bilgileri</p>
            <label>Üyelik Numarası</label>
            <?php if ( ! empty( $uye_no ) ) : ?>
                <div class="uye-no-display"><?php echo esc_html( $uye_no ); ?></div>
                <input type="hidden" name="uye_no_override" value="">
            <?php else : ?>
                <input type="text" name="uye_no_override" value="" class="regular-text"
                       placeholder="Kaydedince otomatik atanır">
            <?php endif; ?>
        </div>

        <div>
            <label>Kayıt Tarihi</label>
            <input type="date" name="uye_kayit_tarihi"
                   value="<?php echo esc_attr( $kayit_tarihi ?: date( 'Y-m-d' ) ); ?>">
        </div>

        <div class="full">
            <p class="uye-section-title">Kişisel Bilgiler</p>
        </div>

        <div>
            <label>TC Kimlik No</label>
            <input type="text" name="uye_tc" value="<?php echo esc_attr( $tc ); ?>"
                   maxlength="11" placeholder="00000000000">
        </div>

        <div>
            <label>E-posta</label>
            <input type="email" name="uye_email" value="<?php echo esc_attr( $email ); ?>"
                   placeholder="ornek@email.com">
        </div>

        <div>
            <label>Cep Telefonu</label>
            <input type="tel" name="uye_telefon" value="<?php echo esc_attr( $telefon ); ?>"
                   placeholder="05xx xxx xx xx">
        </div>

        <div>
            <label>Sabit Telefon</label>
            <input type="tel" name="uye_sabit_tel" value="<?php echo esc_attr( $sabit_tel ); ?>"
                   placeholder="0xxx xxx xx xx">
        </div>

        <div>
            <label>Faks</label>
            <input type="tel" name="uye_faks" value="<?php echo esc_attr( $faks ); ?>"
                   placeholder="0xxx xxx xx xx">
        </div>

        <div class="full">
            <label>Adres</label>
            <textarea name="uye_adres" rows="2"><?php echo esc_textarea( $adres ); ?></textarea>
        </div>

        <div class="full">
            <p class="uye-section-title">İş Bilgileri</p>
        </div>

        <div class="full">
            <label>Çalıştığı Kurum / Üniversite</label>
            <input type="text" name="uye_kurum" value="<?php echo esc_attr( $kurum ); ?>"
                   placeholder="Hastane / Üniversite adı">
        </div>

        <div class="full">
            <label>İş Adresi</label>
            <textarea name="uye_is_adres" rows="2"><?php echo esc_textarea( $is_adres ); ?></textarea>
        </div>

        <div class="full">
            <p class="uye-section-title">Kayıt Durumu</p>
        </div>

        <div>
            <label>Derbis Kaydı</label>
            <select name="uye_derbis">
                <option value="hayir" <?php selected( $derbis, 'hayir' ); ?>>Hayır</option>
                <option value="evet"  <?php selected( $derbis, 'evet'  ); ?>>Evet — Kayıt Yapıldı</option>
            </select>
        </div>

        <div class="full">
            <label>Üye Belgeleri (Teslim Edilenler)</label>
            <div class="uye-belgeler-list">
                <?php foreach ( $belge_options as $key => $label ) : ?>
                    <label>
                        <input type="checkbox" name="uye_belgeler[]"
                               value="<?php echo esc_attr( $key ); ?>"
                               <?php checked( in_array( $key, $belgeler, true ), true ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <?php
}

// ─────────────────────────────────────────────
// 5. Meta Kaydetme
// ─────────────────────────────────────────────
function artroplasti_save_uye_meta( $post_id ) {
    if ( ! isset( $_POST['artroplasti_uye_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['artroplasti_uye_meta_nonce'], 'artroplasti_uye_meta_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_fields = array(
        'uye_email'       => 'sanitize_email',
        'uye_telefon'     => 'sanitize_text_field',
        'uye_tc'          => 'sanitize_text_field',
        'uye_kayit_tarihi'=> 'sanitize_text_field',
        'uye_adres'       => 'sanitize_textarea_field',
        'uye_sabit_tel'   => 'sanitize_text_field',
        'uye_faks'        => 'sanitize_text_field',
        'uye_is_adres'    => 'sanitize_textarea_field',
        'uye_kurum'       => 'sanitize_text_field',
        'uye_derbis'      => 'sanitize_text_field',
    );

    foreach ( $text_fields as $field => $sanitizer ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, $sanitizer( $_POST[ $field ] ) );
        }
    }

    // Belgeler (checkbox dizisi)
    $allowed_belgeler = array( 'basvuru_formu', 'nufus_cuzdani', 'fotograf', 'uzmanlik_belgesi', 'referans_imzasi' );
    $belgeler_raw     = isset( $_POST['uye_belgeler'] ) ? (array) $_POST['uye_belgeler'] : array();
    $belgeler_clean   = array_intersect( $belgeler_raw, $allowed_belgeler );
    update_post_meta( $post_id, 'uye_belgeler', $belgeler_clean );

    // Manuel üye no override (opsiyonel, genelde boş)
    if ( ! empty( $_POST['uye_no_override'] ) ) {
        $existing_no = get_post_meta( $post_id, 'uye_no', true );
        if ( empty( $existing_no ) ) {
            update_post_meta( $post_id, 'uye_no', sanitize_text_field( $_POST['uye_no_override'] ) );
        }
    }
}
add_action( 'save_post_dernek_uye', 'artroplasti_save_uye_meta' );
