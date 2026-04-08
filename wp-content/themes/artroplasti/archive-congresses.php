<?php
/**
 * Archive template for Congresses
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
                  <h4><?php echo esc_html__('Kongreler', 'artroplasti'); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html__('Kongreler', 'artroplasti'); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- congresses section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
            <?php if (have_posts()) : ?>
               <?php while (have_posts()) : the_post(); ?>
                  <?php
                  $congress_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                  if (empty($congress_image)) {
                     $congress_image = get_template_directory_uri() . '/assets/images/default-blog.jpg';
                  }
                  $congress_date = get_post_meta(get_the_ID(), 'congress_date', true);
                  $congress_location = get_post_meta(get_the_ID(), 'congress_location', true);
                  $congress_site_url = get_post_meta(get_the_ID(), 'congress_site_url', true);
                  $congress_program_url = get_post_meta(get_the_ID(), 'congress_program_url', true);
                  $congress_site_label = get_post_meta(get_the_ID(), 'congress_site_label', true);
                  $congress_program_label = get_post_meta(get_the_ID(), 'congress_program_label', true);
                  $congress_website_url = get_post_meta(get_the_ID(), 'congress_website_url', true);
                  $congress_website_label = get_post_meta(get_the_ID(), 'congress_website_label', true);

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
                  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                  <div class="congress-archive-item mb-5" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                     <div class="row align-items-center">
                        <div class="col-lg-12">
                           <div class="congress-archive-image">
                              <img src="<?php echo esc_url($congress_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 100%; border-radius: 8px;">
                           </div>
                           <div class="congress-archive-content">
                              <div class="congress-header-bg mt-2" style="background: #e3f2fd; padding:10px 15px; border-radius: 8px; margin-bottom: 15px;">
                                 <h2 style="margin: 0; font-size: 18px; color: #333;"><?php echo esc_html(get_the_title()); ?></h2>
                              </div>

                              <?php if (!empty($congress_date)) : ?>
                                 <p style="margin-bottom: 10px;">
                                    <i class="fas fa-calendar" style="color: #999; margin-right: 8px;"></i>
                                    <strong><?php echo esc_html__('Tarih:', 'artroplasti'); ?></strong> <?php echo esc_html($congress_date); ?>
                                 </p>
                              <?php endif; ?>

                              <?php if (!empty($congress_location)) : ?>
                                 <p style="margin-bottom: 20px;">
                                    <i class="fas fa-map-marker-alt" style="color: #e91e63; margin-right: 8px;"></i>
                                    <strong><?php echo esc_html__('Yer:', 'artroplasti'); ?></strong> <?php echo esc_html($congress_location); ?>
                                 </p>
                              <?php endif; ?>

                              <div class="congress-archive-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                 <?php if (!empty($congress_site_url)) : ?>
                                    <a href="<?php echo esc_url($congress_site_url); ?>" class="button-btn px-3 py-2 h-auto line-height-normal" target="_blank" rel="noopener">
                                       <?php echo esc_html($congress_site_label); ?>
                                    </a>
                                 <?php endif; ?>

                                 <?php if (!empty($congress_program_url)) : ?>
                                    <a href="<?php echo esc_url($congress_program_url); ?>" class="button-btn px-3 py-2 h-auto line-height-normal" target="_blank" rel="noopener">
                                       <?php echo esc_html($congress_program_label); ?>
                                    </a>
                                 <?php endif; ?>

                                 <?php if (!empty($congress_website_url)) : ?>
                                    <a href="<?php echo esc_url($congress_website_url); ?>" class="button-btn px-3 py-2  h-auto line-height-normal" target="_blank" rel="noopener">
                                       <?php echo esc_html($congress_website_label); ?>
                                    </a>
                                 <?php endif; ?>

                                 <a href="<?php echo esc_url(get_permalink()); ?>" class="button-btn px-3 py-2 h-auto line-height-normal" style="background: #f5f5f5; color: #333; border: 1px solid #ddd;">
                                    <?php echo esc_html__('Detay', 'artroplasti'); ?>
                                 </a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               <?php endwhile; ?>

               <div class="row mt-5">
                  <div class="col-lg-12 col-md-12 col-12">
                     <nav aria-label="Page navigation" class="page-navigation">
                        <?php
                        echo paginate_links(array(
                           'type'      => 'list',
                           'mid_size'  => 2,
                           'prev_text' => '<i class="fas fa-angle-left"></i>',
                           'next_text' => '<i class="fas fa-angle-right"></i>',
                        ));
                        ?>
                     </nav>
                  </div>
               </div>
            <?php else : ?>
               <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <p class="text-center"><?php echo esc_html__('Henüz kongre bulunamadı.', 'artroplasti'); ?></p>
               </div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
