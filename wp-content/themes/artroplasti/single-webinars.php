<?php
/**
 * Single Webinar template
 */

get_header();
?>

<!-- breadcrumb  start-->
<div class="contact-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="sb-contact-section">
               <nav aria-label="breadcrumb">
                  <h4><?php the_title(); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(get_post_type_archive_link('webinars')); ?>"><?php echo esc_html__('Webinarlar', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- single webinar section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <?php 
            // Get all webinar years for sidebar
            $webinar_years = new WP_Query(array(
               'post_type'      => 'webinars',
               'posts_per_page' => -1,
               'orderby'        => 'meta_value_num',
               'meta_key'       => 'webinar_year',
               'order'          => 'DESC',
            ));

            $years = array();
            if ($webinar_years->have_posts()) {
               while ($webinar_years->have_posts()) {
                  $webinar_years->the_post();
                  $year = get_post_meta(get_the_ID(), 'webinar_year', true);
                  if ($year && !in_array($year, $years)) {
                     $years[] = $year;
                  }
               }
            }
            wp_reset_postdata();
            ?>
            
            <?php while (have_posts()) : the_post(); ?>
               <?php
               $webinar_year = get_post_meta(get_the_ID(), 'webinar_year', true);
               ?>
               <div class="webinar-single-wrapper">
                  <div class="webinar-single-header" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px;">
                     <h1 style="margin: 0 0 15px 0; font-size: 32px; color: #333;">
                        <?php echo esc_html(get_the_title()); ?>
                     </h1>
                     <?php if (!empty($webinar_year)) : ?>
                     <p style="margin: 0; color: #999; font-size: 14px;">
                        <i class="fas fa-calendar" style="margin-right: 8px; color: #B81838;"></i>
                        <?php echo esc_html($webinar_year . ' ' . __('Eğitim Webinarı', 'artroplasti')); ?>
                     </p>
                     <?php endif; ?>
                  </div>

                  <div class="webinar-single-content" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                     <?php the_content(); ?>
                  </div>

                  <?php
                  // Get related webinars from same year
                  $related_webinars = new WP_Query(array(
                     'post_type'      => 'webinars',
                     'posts_per_page' => 5,
                     'post__not_in'   => array(get_the_ID()),
                     'meta_query'     => array(
                        array(
                           'key'     => 'webinar_year',
                           'value'   => $webinar_year,
                           'compare' => '=',
                           'type'    => 'NUMERIC',
                        ),
                     ),
                  ));

                  if ($related_webinars->have_posts()) :
                  ?>
                  <div class="webinar-related mt-5" style="background: #f9f9f9; padding: 30px; border-radius: 8px;">
                     <h3 style="margin-bottom: 20px; font-size: 22px;">
                        <?php echo esc_html__('Aynı Yıldan Diğer Webinarlar', 'artroplasti'); ?>
                     </h3>
                     <div class="row">
                        <?php while ($related_webinars->have_posts()) : $related_webinars->the_post(); ?>
                           <div class="col-lg-6 col-md-12 mb-3">
                              <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #B81838; height: 100%;">
                                 <h5 style="margin-bottom: 10px; line-height: 1.4;">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" style="color: #333; text-decoration: none;">
                                       <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                 </h5>
                                 <p style="margin: 0 0 12px 0; color: #666; font-size: 13px; line-height: 1.5;">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 12, '...')); ?>
                                 </p>
                                 <a href="<?php echo esc_url(get_permalink()); ?>" style="color: #B81838; text-decoration: none; font-size: 13px; font-weight: 600;">
                                    <?php echo esc_html__('Detayları Gör', 'artroplasti'); ?> →
                                 </a>
                              </div>
                           </div>
                        <?php endwhile; ?>
                     </div>
                  </div>
                  <?php
                  endif;
                  wp_reset_postdata();
                  ?>

                  <div class="webinar-navigation mt-5 pt-5" style="border-top: 1px solid #eee;">
                     <div class="row">
                        <div class="col-md-6">
                           <a href="<?php echo esc_url(get_post_type_archive_link('webinars')); ?>" class="button-btn" style="display: inline-block; background: #f5f5f5; color: #333; border: 1px solid #ddd;">
                              <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                              <?php echo esc_html__('Tüm Webinarlar', 'artroplasti'); ?>
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
            <?php endwhile; ?>
         </div>

         <div class="col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="webinar-sidebar" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
               <h6 style="margin-bottom: 20px; font-size: 16px; font-weight: 600; text-transform: uppercase;"><?php echo esc_html__('Eğitim Yılları', 'artroplasti'); ?></h6>
               <div style="display: flex; flex-direction: column; gap: 8px;">
                  <?php foreach ($years as $year) : ?>
                     <a href="<?php echo esc_url(add_query_arg('yil', $year, get_post_type_archive_link('webinars'))); ?>" 
                        class="webinar-year-btn" 
                        style="background: #f5f5f5; color: #333; border: 1px solid #ddd; text-decoration: none; padding: 12px 15px; border-radius: 6px; transition: all 0.3s; text-align: center; font-weight: 500;">
                        <?php echo esc_html($year); ?>
                     </a>
                  <?php endforeach; ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
