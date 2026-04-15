<?php
/**
 * Template Name: Contact Page
 */

if (!defined('ABSPATH')) {
    exit;
}

// ── Handle form submission ───────────────────────────────────────────────────
$contact_sent  = false;
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_nonce'])) {
    if (!wp_verify_nonce($_POST['contact_nonce'], 'artroplasti_contact')) {
        $contact_error = __('Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.', 'artroplasti');
    } else {
        $name    = sanitize_text_field($_POST['contact_name'] ?? '');
        $email   = sanitize_email($_POST['contact_email'] ?? '');
        $subject = sanitize_text_field($_POST['contact_subject'] ?? '');
        $message = sanitize_textarea_field($_POST['contact_message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $contact_error = __('Lütfen tüm zorunlu alanları doldurun.', 'artroplasti');
        } elseif (!is_email($email)) {
            $contact_error = __('Geçerli bir e-posta adresi girin.', 'artroplasti');
        } else {
            $to      = get_theme_mod('artroplasti_contact_email', get_option('admin_email'));
            $headers = [
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . $name . ' <' . $email . '>',
                'Reply-To: ' . $email,
            ];
            $body    = "Ad Soyad: {$name}\nE-posta: {$email}\n\n{$message}";
            $sent    = wp_mail($to, $subject ?: __('İletişim Formu Mesajı', 'artroplasti'), $body, $headers);
            if ($sent) {
                $contact_sent = true;
            } else {
                $contact_error = __('Mesaj gönderilemedi. Lütfen tekrar deneyin.', 'artroplasti');
            }
        }
    }
}

get_header();
?>

<!-- breadcrumb start -->
<div class="contact-main-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="sb-contact-section">
                    <nav aria-label="breadcrumb">
                        <h4><?php the_title(); ?></h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                            <?php
                            if ($post->post_parent) {
                                $parent_id = $post->post_parent;
                                $parent_title = get_the_title($parent_id);
                                $parent_url = get_permalink($parent_id);
                                echo '<li class="breadcrumb-item"><a href="' . esc_url($parent_url) . '">' . esc_html($parent_title) . '</a></li>';
                            }
                            ?>
                            <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb end -->

<div class="form-main-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                <div class="social-media-section">
                    <?php
                    $contact_phone = get_theme_mod('artroplasti_contact_phone', '+90 (000) 000 00 00');
                    $contact_email = get_theme_mod('artroplasti_contact_email', 'dernek@artroplasti.org.tr');
                    $contact_address = get_theme_mod('artroplasti_contact_address', 'Adres bilgisi');
                    
                    if (!empty($contact_phone) && trim($contact_phone) !== '') :
                    ?>
                    <section>
                        <span>
                            <i class="fas fa-phone"></i>
                        </span>
                        <div>
                            <h6><?php echo esc_html__('Telefon', 'artroplasti'); ?></h6>
                            <p><a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a></p>
                        </div>
                    </section>
                    <?php endif; ?>
                    
                    <?php if (!empty($contact_email) && trim($contact_email) !== '') : ?>
                    <section>
                        <span class="mr-0">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <div>
                            <h6><?php echo esc_html__('E-posta', 'artroplasti'); ?></h6>
                            <p><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                        </div>
                    </section>
                    <?php endif; ?>
                    
                    <?php if (!empty($contact_address) && trim($contact_address) !== '') : ?>
                    <section>
                        <span>
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <div>
                            <h6><?php echo esc_html__('Adres', 'artroplasti'); ?></h6>
                            <p><?php echo esc_html($contact_address); ?></p>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 col-12">
                <div class="form-section">
                    <h6 class="text-white"><?php echo esc_html__('İletişim Formu', 'artroplasti'); ?></h6>
                    <div class="form-input plr-15">
                        <?php if ($contact_sent) : ?>
                           <div class="contact-success-msg">
                              <i class="fas fa-check-circle"></i>
                              <?php echo esc_html__('Mesajınız başarıyla gönderildi. En kısa sürede dönüş yapacağız.', 'artroplasti'); ?>
                           </div>
                        <?php else : ?>
                           <?php if ($contact_error) : ?>
                              <div class="contact-error-msg"><?php echo esc_html($contact_error); ?></div>
                           <?php endif; ?>
                           <form method="post" class="contact-form" novalidate>
                              <?php wp_nonce_field('artroplasti_contact', 'contact_nonce'); ?>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="contact-field">
                                       <label for="contact_name"><?php echo esc_html__('Ad Soyad', 'artroplasti'); ?> <span>*</span></label>
                                       <input type="text" id="contact_name" name="contact_name" value="<?php echo esc_attr($_POST['contact_name'] ?? ''); ?>" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="contact-field">
                                       <label for="contact_email"><?php echo esc_html__('E-posta', 'artroplasti'); ?> <span>*</span></label>
                                       <input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr($_POST['contact_email'] ?? ''); ?>" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="contact-field">
                                 <label for="contact_subject"><?php echo esc_html__('Konu', 'artroplasti'); ?></label>
                                 <input type="text" id="contact_subject" name="contact_subject" value="<?php echo esc_attr($_POST['contact_subject'] ?? ''); ?>">
                              </div>
                              <div class="contact-field">
                                 <label for="contact_message"><?php echo esc_html__('Mesajınız', 'artroplasti'); ?> <span>*</span></label>
                                 <textarea id="contact_message" name="contact_message" rows="6" required><?php echo esc_textarea($_POST['contact_message'] ?? ''); ?></textarea>
                              </div>
                              <button type="submit" class="button-btn contact-submit">
                                 <?php echo esc_html__('Gönder', 'artroplasti'); ?>
                              </button>
                           </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
