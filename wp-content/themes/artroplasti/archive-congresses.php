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
      <div class="congresses-list">
            <?php if (have_posts()) : ?>
               <?php while (have_posts()) : the_post(); ?>
                  <?php
                  $congress_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                  if (empty($congress_image)) {
                     $congress_image = artroplasti_default_thumb();
                  }
                  $congress_date     = get_post_meta(get_the_ID(), 'congress_date', true);
                  $congress_location = get_post_meta(get_the_ID(), 'congress_location', true);

                  // collect all links in display order
                  $all_links = [];

                  $site_url   = get_post_meta(get_the_ID(), 'congress_site_url', true);
                  $site_label = get_post_meta(get_the_ID(), 'congress_site_label', true) ?: __('Web Sitesi', 'artroplasti');
                  if (!empty($site_url)) {
                     $all_links[] = ['url' => $site_url, 'label' => $site_label];
                  }

                  $program_url   = get_post_meta(get_the_ID(), 'congress_program_url', true);
                  $program_label = get_post_meta(get_the_ID(), 'congress_program_label', true) ?: __('Kongre Programı', 'artroplasti');
                  if (!empty($program_url)) {
                     $all_links[] = ['url' => $program_url, 'label' => $program_label];
                  }

                  $website_url   = get_post_meta(get_the_ID(), 'congress_website_url', true);
                  $website_label = get_post_meta(get_the_ID(), 'congress_website_label', true) ?: __('Detay', 'artroplasti');
                  if (!empty($website_url)) {
                     $all_links[] = ['url' => $website_url, 'label' => $website_label];
                  }

                  $extra_links_json = get_post_meta(get_the_ID(), 'congress_extra_links', true);
                  if (!empty($extra_links_json)) {
                     $extras = json_decode($extra_links_json, true);
                     if (is_array($extras)) {
                        foreach ($extras as $extra) {
                           if (!empty($extra['url'])) {
                              $all_links[] = $extra;
                           }
                        }
                     }
                  }
                  ?>
                  <div class="congress-archive-card">
                     <div class="congress-archive-card-inner">
                        <div class="congress-archive-img">
                           <img src="<?php echo esc_url($congress_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                        </div>
                        <div class="congress-archive-body">
                           <div class="congress-archive-title-bar">
                              <h2><?php echo esc_html(get_the_title()); ?></h2>
                           </div>
                           <div class="congress-archive-meta">
                              <?php if (!empty($congress_date)) : ?>
                                 <div class="congress-meta-row">
                                    <i class="far fa-calendar-alt"></i>
                                    <span class="congress-meta-label"><?php echo esc_html__('Tarih', 'artroplasti'); ?></span>
                                    <span class="congress-meta-value"><?php echo esc_html($congress_date); ?></span>
                                 </div>
                              <?php endif; ?>
                              <?php if (!empty($congress_location)) : ?>
                                 <div class="congress-meta-row">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="congress-meta-label"><?php echo esc_html__('Yer', 'artroplasti'); ?></span>
                                    <span class="congress-meta-value"><?php echo esc_html($congress_location); ?></span>
                                 </div>
                              <?php endif; ?>
                           </div>
                           <?php if (!empty($all_links)) : ?>
                              <div class="congress-archive-links">
                                 <?php foreach ($all_links as $link) : ?>
                                    <a href="<?php echo esc_url($link['url']); ?>" class="congress-link-btn" target="_blank" rel="noopener">
                                       <?php echo esc_html($link['label']); ?>
                                    </a>
                                 <?php endforeach; ?>
                                 <a href="<?php echo esc_url(get_permalink()); ?>" class="congress-link-btn congress-link-btn--detail">
                                    <?php echo esc_html__('Detay', 'artroplasti'); ?>
                                 </a>
                              </div>
                           <?php else : ?>
                              <div class="congress-archive-links">
                                 <a href="<?php echo esc_url(get_permalink()); ?>" class="congress-link-btn congress-link-btn--detail">
                                    <?php echo esc_html__('Detay', 'artroplasti'); ?>
                                 </a>
                              </div>
                           <?php endif; ?>
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
               <p class="text-center"><?php echo esc_html__('Henüz kongre bulunamadı.', 'artroplasti'); ?></p>
            <?php endif; ?>
      </div>
   </div>
</div>
</div>

<?php
get_footer();
