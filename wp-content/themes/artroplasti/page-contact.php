<?php
/**
 * Template Name: Contact Page
 */

if (!defined('ABSPATH')) {
    exit;
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
                        <?php
                        while (have_posts()) : the_post();
                            the_content();
                        endwhile;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
