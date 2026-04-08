<?php
/**
 * İş Bankası Sanal POS Entegrasyonu
 *
 * Hazırlık modunda çalışır; API bilgileri girilince aktif hale gelir.
 *
 * Kullanım:
 *   Ayarlar > İş Bankası POS  sayfasından API bilgilerini girin.
 *   Ödeme formu:  [aidat_odeme_formu]  short-code ile sayfaya ekleyin.
 *
 * İş Bankası API dökümantasyonu:
 *   https://sanalpos.isbank.com.tr/dokumanlar
 *
 * ÖNEMLİ GÜVENLİK NOTU:
 *   - API şifresi options tablosunda encrypt edilmiş saklanır.
 *   - Tüm POST verileri doğrulanır (nonce + sanitize).
 *   - HTTPS zorunludur; HTTP ile form gönderimi engellenir.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────
// 1. Ayarlar Sayfası
// ─────────────────────────────────────────────
function artroplasti_isbank_menu() {
    add_options_page(
        'İş Bankası Sanal POS',
        'İş Bankası POS',
        'manage_options',
        'isbank-pos-ayarlari',
        'artroplasti_isbank_ayarlar_page'
    );
}
add_action( 'admin_menu', 'artroplasti_isbank_menu' );

function artroplasti_isbank_ayarlar_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_POST['isbank_nonce'] )
         && wp_verify_nonce( $_POST['isbank_nonce'], 'isbank_ayar_save' ) ) {

        update_option( 'isbank_merchant_id',  sanitize_text_field( $_POST['isbank_merchant_id'] ?? '' ) );
        update_option( 'isbank_terminal_id',  sanitize_text_field( $_POST['isbank_terminal_id'] ?? '' ) );
        update_option( 'isbank_pos_id',        sanitize_text_field( $_POST['isbank_pos_id'] ?? '' ) );
        update_option( 'isbank_test_mode',    isset( $_POST['isbank_test_mode'] ) ? '1' : '0' );

        // Şifreyi encrypt ederek sakla
        if ( ! empty( $_POST['isbank_password'] ) ) {
            $encrypted = base64_encode( openssl_encrypt(
                sanitize_text_field( $_POST['isbank_password'] ),
                'AES-256-CBC',
                wp_salt( 'auth' ),
                0,
                substr( wp_salt( 'secure_auth' ), 0, 16 )
            ) );
            update_option( 'isbank_password_enc', $encrypted );
        }

        echo '<div class="updated notice"><p><strong>İş Bankası POS ayarları kaydedildi.</strong></p></div>';
    }

    $mid       = get_option( 'isbank_merchant_id', '' );
    $tid       = get_option( 'isbank_terminal_id', '' );
    $pid       = get_option( 'isbank_pos_id', '' );
    $test      = get_option( 'isbank_test_mode', '1' );
    $has_pass  = ! empty( get_option( 'isbank_password_enc', '' ) );
    ?>
    <div class="wrap">
        <h1>İş Bankası Sanal POS Ayarları</h1>

        <?php if ( ! is_ssl() ) : ?>
            <div class="notice notice-error">
                <p><strong>⚠ Uyarı:</strong> Siten şu anda HTTPS üzerinde çalışmıyor.
                Ödeme işlemleri için SSL sertifikası zorunludur.</p>
            </div>
        <?php endif; ?>

        <div class="notice notice-info">
            <p>API bilgilerinizi İş Bankası Sanal POS başvurunuzdan alabilirsiniz.
            Test ortamı için <code>isbank_test_mode</code> seçeneğini aktif tutun.</p>
        </div>

        <form method="post" style="max-width:600px;">
            <?php wp_nonce_field( 'isbank_ayar_save', 'isbank_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="isbank_merchant_id">Merchant ID</label></th>
                    <td><input type="text" id="isbank_merchant_id" name="isbank_merchant_id"
                               value="<?php echo esc_attr( $mid ); ?>" class="regular-text"
                               placeholder="İş Bankası'ndan gelen Merchant ID"></td>
                </tr>
                <tr>
                    <th><label for="isbank_terminal_id">Terminal ID</label></th>
                    <td><input type="text" id="isbank_terminal_id" name="isbank_terminal_id"
                               value="<?php echo esc_attr( $tid ); ?>" class="regular-text"
                               placeholder="İş Bankası'ndan gelen Terminal ID"></td>
                </tr>
                <tr>
                    <th><label for="isbank_pos_id">POS ID</label></th>
                    <td><input type="text" id="isbank_pos_id" name="isbank_pos_id"
                               value="<?php echo esc_attr( $pid ); ?>" class="regular-text"
                               placeholder="İş Bankası'ndan gelen POS ID"></td>
                </tr>
                <tr>
                    <th><label for="isbank_password">API Şifresi</label></th>
                    <td>
                        <input type="password" id="isbank_password" name="isbank_password"
                               class="regular-text" placeholder="<?php echo $has_pass ? '••••••••••• (değiştirmek için girin)' : 'API Şifresi'; ?>">
                        <?php if ( $has_pass ) : ?>
                            <p class="description">Şifre kayıtlı. Değiştirmek istemiyorsanız boş bırakın.</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Mod</th>
                    <td>
                        <label>
                            <input type="checkbox" name="isbank_test_mode" value="1"
                                   <?php checked( $test, '1' ); ?>>
                            <strong>Test Modu</strong> (canlıya geçmeden önce test edin)
                        </label>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Ayarları Kaydet">
            </p>
        </form>

        <hr>
        <h2>Ödeme Formu Kısa Kodu</h2>
        <p>Ödeme sayfanıza şu kısa kodu ekleyin:</p>
        <pre style="background:#f0f0f0;padding:12px;display:inline-block;">[aidat_odeme_formu]</pre>
        <p>Callback / bildirim URL'si (İş Bankası panelinde tanımlanması gereken):</p>
        <pre style="background:#f0f0f0;padding:12px;display:inline-block;"><?php echo esc_url( home_url( '/aidat-odeme-callback/' ) ); ?></pre>
    </div>
    <?php
}

// ─────────────────────────────────────────────
// 2. Yardımcı: Şifre Çözme
// ─────────────────────────────────────────────
function artroplasti_isbank_get_password() {
    $enc = get_option( 'isbank_password_enc', '' );
    if ( empty( $enc ) ) {
        return '';
    }
    $dec = openssl_decrypt(
        base64_decode( $enc ),
        'AES-256-CBC',
        wp_salt( 'auth' ),
        0,
        substr( wp_salt( 'secure_auth' ), 0, 16 )
    );
    return $dec ?: '';
}

// ─────────────────────────────────────────────
// 3. Short-code: Ödeme Formu
// ─────────────────────────────────────────────
function artroplasti_aidat_odeme_formu_shortcode( $atts ) {

    if ( ! is_ssl() ) {
        return '<div class="alert alert-danger">Güvenli bağlantı (HTTPS) gereklidir. Lütfen yöneticinizle iletişime geçin.</div>';
    }

    $mid = get_option( 'isbank_merchant_id', '' );
    if ( empty( $mid ) ) {
        return '<div class="alert alert-warning">Ödeme ayarları henüz yapılandırılmamış. Lütfen site yöneticinizle iletişime geçin.</div>';
    }

    // Bu yıl ve önceki yıllar için dropdown
    $yillar = artroplasti_aidat_yillari();
    $yil_simdiki = (int) date( 'Y' );

    ob_start();
    ?>
    <div class="aidat-odeme-wrapper" style="max-width:520px;margin:0 auto;">
        <h3 style="margin-bottom:20px;">Yıllık Aidat Ödemesi</h3>

        <?php if ( isset( $_GET['odeme'] ) ) :
            if ( $_GET['odeme'] === 'basarili' ) : ?>
                <div class="alert alert-success" style="padding:12px;background:#d4edda;border-radius:6px;margin-bottom:20px;">
                    ✓ Ödemeniz başarıyla alındı. Teşekkür ederiz.
                </div>
            <?php elseif ( $_GET['odeme'] === 'basarisiz' ) : ?>
                <div class="alert alert-danger" style="padding:12px;background:#f8d7da;border-radius:6px;margin-bottom:20px;">
                    ✗ Ödeme işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form id="aidat-odeme-form" method="post"
              action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
            <?php wp_nonce_field( 'aidat_odeme_nonce', 'aidat_odeme_nonce' ); ?>
            <input type="hidden" name="action" value="aidat_odeme_baslat">

            <p>
                <label for="aidat_ad_soyad"><strong>Ad Soyad *</strong></label>
                <input type="text" id="aidat_ad_soyad" name="aidat_ad_soyad"
                       required class="form-control"
                       style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;"
                       placeholder="Ad Soyad">
            </p>
            <p>
                <label for="aidat_email"><strong>E-posta *</strong></label>
                <input type="email" id="aidat_email" name="aidat_email"
                       required class="form-control"
                       style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;"
                       placeholder="ornek@email.com">
            </p>
            <p>
                <label for="aidat_yil"><strong>Ödeme Yapılacak Yıl *</strong></label>
                <select id="aidat_yil" name="aidat_yil" required
                        style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;">
                    <option value="">— Yıl Seçin —</option>
                    <?php foreach ( $yillar as $yil ) :
                        $tutar = get_option( 'aidat_tutar_' . $yil, '' );
                        ?>
                        <option value="<?php echo esc_attr( $yil ); ?>" data-tutar="<?php echo esc_attr( $tutar ); ?>">
                            <?php echo esc_html( $yil ); ?>
                            <?php if ( $tutar ) echo ' — ' . number_format( (float) $tutar, 2, ',', '.' ) . ' ₺'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <div id="aidat-tutar-bilgi" style="display:none;background:#fff8e1;padding:10px 14px;
                 border-radius:6px;margin:10px 0;font-weight:600;font-size:16px;color:#333;">
                Ödenecek Tutar: <span id="aidat-tutar-goster">—</span>
            </div>

            <p>
                <label for="aidat_kart_no"><strong>Kart Numarası *</strong></label>
                <input type="text" id="aidat_kart_no" name="aidat_kart_no"
                       required maxlength="19" pattern="[\d ]{13,19}"
                       class="form-control"
                       style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;"
                       placeholder="0000 0000 0000 0000"
                       autocomplete="cc-number">
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <p style="margin:0;">
                    <label for="aidat_kart_ay"><strong>Son Kullanma Tarihi *</strong></label>
                    <input type="text" id="aidat_kart_ay" name="aidat_kart_ay"
                           required maxlength="5" pattern="\d{2}/\d{2}"
                           class="form-control"
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;"
                           placeholder="AA/YY" autocomplete="cc-exp">
                </p>
                <p style="margin:0;">
                    <label for="aidat_kart_cvv"><strong>CVV *</strong></label>
                    <input type="text" id="aidat_kart_cvv" name="aidat_kart_cvv"
                           required maxlength="4" pattern="\d{3,4}"
                           class="form-control"
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;"
                           placeholder="CVV" autocomplete="cc-csc">
                </p>
            </div>

            <?php
            // Taksit seçeneği (İş Bankası destekliyorsa)
            $taksit_aktif = get_option( 'isbank_taksit_aktif', '0' );
            if ( $taksit_aktif === '1' ) : ?>
            <p style="margin-top:16px;">
                <label for="aidat_taksit"><strong>Taksit Seçeneği</strong></label>
                <select id="aidat_taksit" name="aidat_taksit"
                        style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;margin-top:4px;">
                    <option value="1">Tek Çekim</option>
                    <option value="2">2 Taksit</option>
                    <option value="3">3 Taksit</option>
                    <option value="6">6 Taksit</option>
                </select>
            </p>
            <?php endif; ?>

            <p style="margin-top:20px;">
                <button type="submit" id="aidat-odeme-btn"
                        style="width:100%;padding:12px;background:#B81838;color:#fff;border:none;
                               border-radius:6px;font-size:16px;font-weight:700;cursor:pointer;
                               transition:background .3s;">
                    Ödemeyi Tamamla
                </button>
            </p>

            <p style="font-size:12px;color:#888;text-align:center;margin-top:8px;">
                🔒 Ödemeniz İş Bankası güvenli altyapısında işlenir. Kart bilgileriniz şifreli aktarılır.
            </p>
        </form>
    </div>

    <script>
    (function($){
        // Yıl değişince tutar güncelle
        $('#aidat_yil').on('change', function(){
            var tutar = $(this).find(':selected').data('tutar');
            if(tutar){
                $('#aidat-tutar-bilgi').show();
                $('#aidat-tutar-goster').text(parseFloat(tutar).toLocaleString('tr-TR',{minimumFractionDigits:2}) + ' ₺');
            } else {
                $('#aidat-tutar-bilgi').hide();
            }
        });

        // Kart numarası formatla
        $('#aidat_kart_no').on('input', function(){
            var val = $(this).val().replace(/\D/g,'').substring(0,16);
            $(this).val(val.replace(/(.{4})/g,'$1 ').trim());
        });

        // SKT formatla
        $('#aidat_kart_ay').on('input', function(){
            var val = $(this).val().replace(/\D/g,'');
            if(val.length >= 3) val = val.substring(0,2) + '/' + val.substring(2,4);
            $(this).val(val);
        });

        // Form gönder
        $('#aidat-odeme-form').on('submit', function(e){
            e.preventDefault();
            var btn = $('#aidat-odeme-btn');
            btn.text('İşleniyor...').prop('disabled', true);

            $.post(
                '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>',
                $(this).serialize(),
                function(res){
                    if(res.success && res.data.form_html){
                        $('body').append(res.data.form_html);
                    } else if(res.success && res.data.redirect){
                        window.location.href = res.data.redirect;
                    } else {
                        var msg = (res.data && res.data.message) ? res.data.message : 'Bir hata oluştu.';
                        alert(msg);
                        btn.text('Ödemeyi Tamamla').prop('disabled', false);
                    }
                }
            ).fail(function(){
                alert('Sunucu bağlantı hatası.');
                btn.text('Ödemeyi Tamamla').prop('disabled', false);
            });
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'aidat_odeme_formu', 'artroplasti_aidat_odeme_formu_shortcode' );

// ─────────────────────────────────────────────
// 4. AJAX: Ödeme Başlat
// ─────────────────────────────────────────────
function artroplasti_aidat_odeme_baslat() {
    // Nonce kontrolü
    if ( ! isset( $_POST['aidat_odeme_nonce'] )
         || ! wp_verify_nonce( $_POST['aidat_odeme_nonce'], 'aidat_odeme_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Güvenlik kontrolü başarısız.' ) );
    }

    if ( ! is_ssl() ) {
        wp_send_json_error( array( 'message' => 'Güvenli bağlantı gereklidir.' ) );
    }

    // Alanları doğrula
    $ad_soyad = sanitize_text_field( $_POST['aidat_ad_soyad'] ?? '' );
    $email    = sanitize_email( $_POST['aidat_email'] ?? '' );
    $yil      = intval( $_POST['aidat_yil'] ?? 0 );
    $taksit   = intval( $_POST['aidat_taksit'] ?? 1 );

    if ( empty( $ad_soyad ) || empty( $email ) || ! $yil ) {
        wp_send_json_error( array( 'message' => 'Lütfen tüm zorunlu alanları doldurun.' ) );
    }

    $yillar_izin = artroplasti_aidat_yillari();
    if ( ! in_array( $yil, $yillar_izin, true ) ) {
        wp_send_json_error( array( 'message' => 'Geçersiz yıl seçimi.' ) );
    }

    $tutar = (float) get_option( 'aidat_tutar_' . $yil, 0 );
    if ( $tutar <= 0 ) {
        wp_send_json_error( array( 'message' => 'Bu yıl için aidat tutarı henüz belirlenmemiş.' ) );
    }

    // Kart bilgileri — sadece API'ye iletmek için, saklamıyoruz
    $kart_no  = preg_replace( '/\D/', '', $_POST['aidat_kart_no'] ?? '' );
    $kart_skt = sanitize_text_field( $_POST['aidat_kart_ay'] ?? '' );
    $kart_cvv = sanitize_text_field( $_POST['aidat_kart_cvv'] ?? '' );

    if ( strlen( $kart_no ) < 13 || empty( $kart_skt ) || empty( $kart_cvv ) ) {
        wp_send_json_error( array( 'message' => 'Lütfen kart bilgilerini eksiksiz girin.' ) );
    }

    // SKT parçala
    $skt_parts = explode( '/', $kart_skt );
    $kart_ay   = str_pad( $skt_parts[0] ?? '', 2, '0', STR_PAD_LEFT );
    $kart_yil  = $skt_parts[1] ?? '';

    // İş Bankası API Bilgileri
    $merchant_id  = get_option( 'isbank_merchant_id', '' );
    $terminal_id  = get_option( 'isbank_terminal_id', '' );
    $pos_id       = get_option( 'isbank_pos_id', '' );
    $api_password = artroplasti_isbank_get_password();
    $test_mode    = get_option( 'isbank_test_mode', '1' ) === '1';

    if ( empty( $merchant_id ) || empty( $terminal_id ) || empty( $api_password ) ) {
        wp_send_json_error( array( 'message' => 'Ödeme sistemi yapılandırılmamış. Yöneticiyle iletişime geçin.' ) );
    }

    // İş Bankası endpoint
    $api_url = $test_mode
        ? 'https://sanalpos.isbank.com.tr/test/servlet/est3Dgate'
        : 'https://sanalpos.isbank.com.tr/servlet/est3Dgate';

    // Sipariş numarası
    $order_id = 'AD-' . $yil . '-' . time();

    // Callback URL
    $callback_url = home_url( '/aidat-odeme-callback/' );

    /*
     * ─── GERÇEK API ÇAĞRISI ───
     * İş Bankası EST 3D Secure entegrasyonu için aşağıdaki parametreler
     * kullanılır. API dökümantasyonu geldiğinde bu bölümü güncelleyin.
     *
     * Mevcut yapı: form POST ile banka sayfasına yönlendirme
     */
    $post_data = array(
        'clientid'       => $merchant_id,
        'storetype'      => '3d_pay',
        'hash'           => artroplasti_isbank_hash( $merchant_id, $order_id, $tutar, 'TRY', 'pay', $api_password ),
        'trantype'       => 'Auth',
        'amount'         => number_format( $tutar, 2, '.', '' ),
        'currency'       => '949', // TRY
        'oid'            => $order_id,
        'okUrl'          => $callback_url . '?sonuc=basarili&yil=' . $yil . '&email=' . urlencode( $email ),
        'failUrl'        => $callback_url . '?sonuc=basarisiz',
        'lang'           => 'tr',
        'encoding'       => 'utf-8',
        'pan'            => $kart_no,
        'Ecom_Payment_Card_ExpDate_Month' => $kart_ay,
        'Ecom_Payment_Card_ExpDate_Year'  => $kart_yil,
        'cv2'            => $kart_cvv,
        'callbackurl'    => $callback_url,
        'taksit'         => $taksit > 1 ? $taksit : '',
        /* Müşteri bilgileri (opsiyonel ama önerilen) */
        'Email'          => $email,
        'BillToName'     => $ad_soyad,
    );

    // Geçici olarak order bilgisini transient ile sakla (callback'te aidat kaydı güncellenir)
    set_transient( 'aidat_order_' . $order_id, array(
        'ad_soyad' => $ad_soyad,
        'email'    => $email,
        'yil'      => $yil,
        'tutar'    => $tutar,
    ), 3600 );

    // Banka formuna yönlendir (hidden form POST yöntemi)
    $form_html  = '<form id="isbank-redirect-form" method="post" action="' . esc_url( $api_url ) . '">';
    foreach ( $post_data as $k => $v ) {
        $form_html .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">';
    }
    $form_html .= '</form>';
    $form_html .= '<script>document.getElementById("isbank-redirect-form").submit();</script>';

    // Bu aşamada form HTML'ini döndür — ön yüz JS bir iFrame/yeni sayfa açar
    // veya direkt yönlendirme için data-redirect kullanılır
    wp_send_json_success( array(
        'form_html' => $form_html,
        'method'    => 'form_post',
    ) );
}
add_action( 'wp_ajax_aidat_odeme_baslat',        'artroplasti_aidat_odeme_baslat' );
add_action( 'wp_ajax_nopriv_aidat_odeme_baslat', 'artroplasti_aidat_odeme_baslat' );

// ─────────────────────────────────────────────
// 5. Hash Hesaplama (İş Bankası EST yöntemi)
// ─────────────────────────────────────────────
function artroplasti_isbank_hash( $client_id, $order_id, $amount, $currency, $tran_type, $store_key ) {
    $hash_str = $client_id . $order_id . $amount . $currency . $tran_type . $store_key;
    return base64_encode( pack( 'H*', sha1( $hash_str ) ) );
}

// ─────────────────────────────────────────────
// 6. Callback — Banka Ödeme Bildirimi
// ─────────────────────────────────────────────
function artroplasti_aidat_callback_handler() {
    // Rewrite kuralıyla /aidat-odeme-callback/ bu fonksiyona gelir
    if ( ! isset( $_GET['_aidat_callback'] ) && strpos( $_SERVER['REQUEST_URI'] ?? '', 'aidat-odeme-callback' ) === false ) {
        return;
    }

    $sonuc = sanitize_text_field( $_GET['sonuc'] ?? $_POST['Response'] ?? '' );
    $yil   = intval( $_GET['yil'] ?? 0 );
    $email = sanitize_email( $_GET['email'] ?? '' );

    // Banka'dan gelen 3DS doğrulama hash kontrolü
    // (İş Bankası dökümantasyonu gelince implement edilecek)
    $odeme_basarili = ( $sonuc === 'basarili' || $sonuc === 'Approved' );

    if ( $odeme_basarili && $yil ) {

        // Aidat kaydını güncelle: e-posta ile üyeyi bul
        if ( ! empty( $email ) ) {
            $aidat_posts = get_posts( array(
                'post_type'      => 'dernek_aidat',
                'post_status'    => 'publish',
                'meta_query'     => array(
                    array(
                        'key'   => 'aidat_email',
                        'value' => $email,
                    ),
                ),
                'posts_per_page' => 1,
            ) );

            if ( ! empty( $aidat_posts ) ) {
                $aidat_id = $aidat_posts[0]->ID;
                update_post_meta( $aidat_id, 'aidat_durum_' . $yil, 'odendi' );
                update_post_meta( $aidat_id, 'aidat_tarih_' . $yil, date( 'Y-m-d' ) );
                update_post_meta( $aidat_id, 'aidat_not_'   . $yil, 'Online ödeme - İş Bankası' );

                // Bildirim e-postası
                $konu   = get_bloginfo( 'name' ) . ' — Aidat Ödemesi Alındı';
                $mesaj  = "Sayın {$aidat_posts[0]->post_title},\n\n";
                $mesaj .= "{$yil} yılı aidat ödemeniz başarıyla alınmıştır.\n\n";
                $mesaj .= "İyi günler dileriz.";
                wp_mail( $email, $konu, $mesaj );
            }
        }

        // Ödeme sayfasına başarılı olarak yönlendir
        $odeme_sayfasi = home_url( '/aidat-odeme/' );
        wp_redirect( add_query_arg( 'odeme', 'basarili', $odeme_sayfasi ) );
        exit;
    }

    // Başarısız
    $odeme_sayfasi = home_url( '/aidat-odeme/' );
    wp_redirect( add_query_arg( 'odeme', 'basarisiz', $odeme_sayfasi ) );
    exit;
}
add_action( 'template_redirect', 'artroplasti_aidat_callback_handler', 1 );

// ─────────────────────────────────────────────
// 7. Callback URL için rewrite kuralı
// ─────────────────────────────────────────────
function artroplasti_aidat_rewrite() {
    add_rewrite_rule(
        '^aidat-odeme-callback/?$',
        'index.php?_aidat_callback=1',
        'top'
    );
}
add_action( 'init', 'artroplasti_aidat_rewrite' );

function artroplasti_aidat_query_vars( $vars ) {
    $vars[] = '_aidat_callback';
    return $vars;
}
add_filter( 'query_vars', 'artroplasti_aidat_query_vars' );
