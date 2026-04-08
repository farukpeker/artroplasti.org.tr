<?php
/**
 * The template for displaying the footer
 */

if (!defined('ABSPATH')) {
    exit;
}

$theme_uri = get_template_directory_uri();
$contact_email = get_theme_mod('artroplasti_contact_email', 'dernek@artroplasti.org.tr');
$contact_phone = get_theme_mod('artroplasti_contact_phone', '+90 (000) 000 00 00');
$contact_address = get_theme_mod('artroplasti_contact_address', 'Adres bilgisi');
$contact_hours = get_theme_mod('artroplasti_contact_hours', "Hafta içi: 09:00 - 18:00\nHafta sonu: 10:00 - 16:00");

$social_facebook  = get_theme_mod('artroplasti_social_facebook', 'https://www.facebook.com');
$social_twitter   = get_theme_mod('artroplasti_social_twitter', 'https://www.twitter.com');
$social_instagram = get_theme_mod('artroplasti_social_instagram', 'https://www.instagram.com');
$social_linkedin  = get_theme_mod('artroplasti_social_linkedin', 'https://www.linkedin.com');
$social_youtube   = get_theme_mod('artroplasti_social_youtube', '');

// Footer Logo
$footer_logo_id  = get_theme_mod('artroplasti_footer_logo');
$footer_logo_url = $footer_logo_id ? wp_get_attachment_image_url($footer_logo_id, 'full') : '';
$footer_logo_alt = $footer_logo_id ? get_post_meta($footer_logo_id, '_wp_attachment_image_alt', true) : '';
?>

<!-- footer section start -->
<div class="footer-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-3 col-md-6 col-sm-12 col-12">
            <div class="sb-footer-section">
               <div class="footer-logo">
                  <?php if (!empty($footer_logo_url)) : ?>
                     <img src="<?php echo esc_url($footer_logo_url); ?>" alt="<?php echo esc_attr($footer_logo_alt ?: get_bloginfo('name')); ?>">
                  <?php else : ?>
                     <img src="<?php echo esc_url($theme_uri . '/assets/images/footer-logo.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                  <?php endif; ?>
               </div>
               <ul>
                  <li>
                     <a href="<?php echo esc_url($contact_address ? '#' : 'javascript:;'); ?>">
                        <i class="fas fa-map-marker-alt"></i><?php echo esc_html(__('Adres', 'artroplasti')); ?><br>
                        <?php echo esc_html($contact_address); ?>
                     </a>
                  </li>
                  <li>
                     <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $contact_phone)); ?>">
                        <i class="fas fa-phone"></i><?php echo esc_html(__('Telefon', 'artroplasti')); ?><br>
                        <?php echo esc_html($contact_phone); ?>
                     </a>
                  </li>
                  <li>
                     <a href="mailto:<?php echo esc_attr($contact_email); ?>">
                        <i class="fas fa-envelope"></i><?php echo esc_html(__('E-posta', 'artroplasti')); ?><br>
                        <?php echo esc_html($contact_email); ?>
                     </a>
                  </li>
                  <li>
                     <ul class="footer-media">
                        <?php if (!empty($social_facebook)) : ?>
                           <li><a href="<?php echo esc_url($social_facebook); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>
                        <?php endif; ?>
                        <?php if (!empty($social_twitter)) : ?>
                           <li>
                              <a href="<?php echo esc_url($social_twitter); ?>" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18" aria-hidden="true" focusable="false">
                                    <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                                 </svg>
                              </a>
                           </li>
                        <?php endif; ?>
                        <?php if (!empty($social_instagram)) : ?>
                           <li><a href="<?php echo esc_url($social_instagram); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
                        <?php endif; ?>
                        <?php if (!empty($social_linkedin)) : ?>
                           <li><a href="<?php echo esc_url($social_linkedin); ?>" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a></li>
                        <?php endif; ?>
                     </ul>
                  </li>
               </ul>
            </div>
         </div>
         <div class="col-lg-2 col-md-6 col-sm-12 col-12">
            <div class="links">
               <h4><?php echo esc_html(__('Site Haritası', 'artroplasti')); ?></h4>
               <?php
               wp_nav_menu(array(
                  'theme_location' => 'footer',
                  'fallback_cb'    => 'wp_page_menu',
                  'container'      => false,
                  'menu_class'     => '',
                  'depth'          => 1,
                  'before'         => '<i class="fas fa-angle-right"></i>',
               ));
               ?>
            </div>
         </div>
         <div class="col-lg-4 col-md-6 col-sm-12 col-12">
            <h4><?php echo esc_html(__('Son Yazılar', 'artroplasti')); ?></h4>
            <div class="img-link">
               <ul>
                  <?php
                  $recent_posts = wp_get_recent_posts(array(
                     'numberposts'      => 4,
                     'post_status'      => 'publish',
                     'suppress_filters' => false,
                     'category_name'    => 'haberler',
                  ));
                  
                  foreach ($recent_posts as $post) {
                     $post_id = $post['ID'];
                     $thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail') ?: $theme_uri . '/assets/images/post-img1.jpg';
                     ?>
                     <li>
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post['post_title']); ?>">
                        <div class="content">
                           <h5 class="pb-0"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($post['post_title']); ?></a></h5>
                        </div>
                     </li>
                     <?php
                  }
                  wp_reset_postdata();
                  ?>
               </ul>
            </div>
         </div>
         <div class="col-lg-3 col-md-6 col-sm-12 col-12">
            <div class="time-wrapper">
               <div>
                  <h4><?php echo esc_html(__('Çalışma Saatleri', 'artroplasti')); ?></h4>
                  <ul>
                     <?php
                     $hours_array = array_filter(array_map('trim', explode("\n", $contact_hours)));
                     foreach ($hours_array as $hour) {
                        echo '<li>' . esc_html($hour) . '</li>';
                     }
                     ?>
                  </ul>
               </div>
            </div>
            <div id="newsletter">
               <h5><?php echo esc_html(__('Bültene Kaydol', 'artroplasti')); ?></h5>
               <div class="input-box">
                <p class="newsletter-description">Güncel haberleri ve duyuruları almak için e-habere abone olun. Yeni gelişmelerden haberdar olun.</p>
                  <form method="post" class="newsletter-form">
                     <?php wp_nonce_field('artroplasti_newsletter', 'newsletter_nonce'); ?>
                     <input type="email" name="newsletter_email" placeholder="<?php echo esc_attr(__('E-posta adresi', 'artroplasti')); ?>" required>
                     <button type="submit" aria-label="<?php echo esc_attr(__('Subscribe', 'artroplasti')); ?>">
                        <i class="fas fa-paper-plane"></i>
                     </button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <section>
      <div class="container">
         <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
               <p>© <?php echo esc_html(date('Y')); ?> - <?php echo esc_html(get_bloginfo('name')); ?> | <?php echo esc_html(__('All Rights Reserved', 'artroplasti')); ?>.</p>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
               <p class="last-para">
                  <?php
                  $privacy_page = get_privacy_policy_url();
                  $privacy_link = $privacy_page ? '<a href="' . esc_url($privacy_page) . '">' . esc_html(__('Privacy Policy', 'artroplasti')) . '</a>' : esc_html(__('Privacy Policy', 'artroplasti'));
                  echo wp_kses_post($privacy_link);
                  ?>
                  | 
                  <a href="<?php echo esc_url(home_url('/terms-and-conditions')); ?>"><?php echo esc_html(__('Terms and Conditions', 'artroplasti')); ?></a>
               </p>
            </div>
            <div class="col-md-12 text-center">
                <p>Designed by <a href="https://medikalajans.com/" target="_blank" rel="noopener">Medikal Ajans</a></p>
            </div>
         </div>
      </div>
   </section>
</div>
<!-- footer section end -->

<?php wp_footer(); ?>
</body>
</html>