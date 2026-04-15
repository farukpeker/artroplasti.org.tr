<?php
/**
 * Single Congress template
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
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(get_post_type_archive_link('congresses')); ?>"><?php echo esc_html__('Kongreler', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- single congress section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <?php while (have_posts()) : the_post(); ?>
               <?php
               $congress_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
               if (empty($congress_image)) {
                  $congress_image = artroplasti_default_thumb();
               }
               $congress_date = get_post_meta(get_the_ID(), 'congress_date', true);
               $congress_location = get_post_meta(get_the_ID(), 'congress_location', true);
               $congress_site_url = get_post_meta(get_the_ID(), 'congress_site_url', true);
               $congress_program_url = get_post_meta(get_the_ID(), 'congress_program_url', true);
               $congress_site_label = get_post_meta(get_the_ID(), 'congress_site_label', true);
               $congress_program_label = get_post_meta(get_the_ID(), 'congress_program_label', true);
               $congress_website_url = get_post_meta(get_the_ID(), 'congress_website_url', true);
               $congress_website_label = get_post_meta(get_the_ID(), 'congress_website_label', true);

               // extra links stored as JSON
               $congress_extra_links = [];
               $extra_links_json = get_post_meta(get_the_ID(), 'congress_extra_links', true);
               if (!empty($extra_links_json)) {
                  $decoded = json_decode($extra_links_json, true);
                  if (is_array($decoded)) {
                     $congress_extra_links = $decoded;
                  }
               }

               if (empty($congress_site_label)) {
                  $congress_site_label = __('Web sitesi', 'artroplasti');
               }

               if (empty($congress_program_label)) {
                  $congress_program_label = __('Kongre Programı', 'artroplasti');
               }

               if (empty($congress_website_label)) {
                  $congress_website_label = __('Web sitesi', 'artroplasti');
               }
               ?>
               <div class="congress-single-wrapper">
                  <div class="row align-items-center mb-5">
                     <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                        <div class="congress-single-image">
                           <img src="<?php echo esc_url($congress_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="img-fluid" style="border-radius: 8px;">
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-12">
                        <div class="congress-single-info">
                           <div class="congress-header-bg" style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                              <h1 style="margin: 0; font-size: 32px; color: #333;"><?php echo esc_html(get_the_title()); ?></h1>
                           </div>

                           <?php if (!empty($congress_date)) : ?>
                              <p style="margin-bottom: 15px; font-size: 16px;">
                                 <i class="fas fa-calendar" style="color: #999; margin-right: 10px; width: 20px;"></i>
                                 <strong><?php echo esc_html__('Tarih:', 'artroplasti'); ?></strong> 
                                 <span><?php echo esc_html($congress_date); ?></span>
                              </p>
                           <?php endif; ?>

                           <?php if (!empty($congress_location)) : ?>
                              <p style="margin-bottom: 25px; font-size: 16px;">
                                 <i class="fas fa-map-marker-alt" style="color: #e91e63; margin-right: 10px; width: 20px;"></i>
                                 <strong><?php echo esc_html__('Yer:', 'artroplasti'); ?></strong> 
                                 <span><?php echo esc_html($congress_location); ?></span>
                              </p>
                           <?php endif; ?>

                           <div class="congress-single-actions">
                              <?php if (!empty($congress_site_url)) : ?>
                                 <a href="<?php echo esc_url($congress_site_url); ?>" class="congress-link-btn" target="_blank" rel="noopener">
                                    <?php echo esc_html($congress_site_label); ?>
                                 </a>
                              <?php endif; ?>

                              <?php if (!empty($congress_program_url)) : ?>
                                 <a href="<?php echo esc_url($congress_program_url); ?>" class="congress-link-btn" target="_blank" rel="noopener">
                                    <?php echo esc_html($congress_program_label); ?>
                                 </a>
                              <?php endif; ?>

                              <?php if (!empty($congress_website_url)) : ?>
                                 <a href="<?php echo esc_url($congress_website_url); ?>" class="congress-link-btn" target="_blank" rel="noopener">
                                    <?php echo esc_html($congress_website_label); ?>
                                 </a>
                              <?php endif; ?>

                              <?php foreach ($congress_extra_links as $extra_link) : ?>
                                 <?php if (!empty($extra_link['url'])) : ?>
                                    <a href="<?php echo esc_url($extra_link['url']); ?>" class="congress-link-btn" target="_blank" rel="noopener">
                                       <?php echo esc_html($extra_link['label']); ?>
                                    </a>
                                 <?php endif; ?>
                              <?php endforeach; ?>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="congress-single-content mt-4">
                     <?php
                     $content = get_the_content();
                     // strip legacy auto-generated plain-text content (empty or only whitespace/p tags)
                     $stripped = wp_strip_all_tags($content);
                     if (!empty(trim($stripped))) :
                     ?>
                     <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <?php the_content(); ?>
                     </div>
                     <?php endif; ?>
                  </div>

                  <?php
                  $prev_congress = get_previous_post();
                  $next_congress = get_next_post();
                  if ($prev_congress || $next_congress) :
                  ?>
                  <div class="congress-navigation mt-5 pt-5" style="border-top: 1px solid #eee;">
                     <div class="row">
                        <div class="col-md-6">
                           <?php if ($prev_congress) : ?>
                              <a href="<?php echo esc_url(get_permalink($prev_congress)); ?>" class="prev-congress" style="display: inline-block; padding: 10px 20px; background: #f5f5f5; border-radius: 5px; text-decoration: none; color: #333;">
                                 <i class="fas fa-angle-left"></i> <?php echo esc_html($prev_congress->post_title); ?>
                              </a>
                           <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                           <?php if ($next_congress) : ?>
                              <a href="<?php echo esc_url(get_permalink($next_congress)); ?>" class="next-congress" style="display: inline-block; padding: 10px 20px; background: #f5f5f5; border-radius: 5px; text-decoration: none; color: #333;">
                                 <?php echo esc_html($next_congress->post_title); ?> <i class="fas fa-angle-right"></i>
                              </a>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
                  <?php endif; ?>
               </div>
            <?php endwhile; ?>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
