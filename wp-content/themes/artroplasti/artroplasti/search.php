<?php
/**
 * Search Results Template
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
                  <h4><?php printf(esc_html__('Arama Sonuçları: %s', 'artroplasti'), get_search_query()); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html__('Arama', 'artroplasti'); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- search results section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <?php if (have_posts()) : ?>
               <div class="search-results-wrapper">
                  <p class="search-results-count" style="margin-bottom: 25px; color: #666; font-size: 14px;">
                     <?php printf(esc_html__('"%s" için %d sonuç bulundu', 'artroplasti'), get_search_query(), $GLOBALS['wp_query']->found_posts); ?>
                  </p>

                  <div class="row">
                     <?php while (have_posts()) : the_post(); ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-12 mb-4">
                           <div class="search-result-item" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column;">
                              <?php 
                              $post_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                              if (!empty($post_image)) :
                              ?>
                              <div class="search-result-image mb-3">
                                 <a href="<?php echo esc_url(get_permalink()); ?>">
                                    <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 100%; border-radius: 6px; object-fit: cover; aspect-ratio: 4/3;">
                                 </a>
                              </div>
                              <?php endif; ?>

                              <div class="search-result-content" style="flex-grow: 1; display: flex; flex-direction: column;">
                                 <div class="search-result-meta" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 12px; color: #999; flex-wrap: wrap; gap: 8px;">
                                    <span>
                                       <i class="fas fa-calendar" style="margin-right: 4px;"></i>
                                       <?php echo esc_html(get_the_date('d.m.Y')); ?>
                                    </span>
                                 </div>

                                 <h3 style="margin-bottom: 10px; font-size: 18px; line-height: 1.4;">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" style="color: #333; text-decoration: none;">
                                       <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                 </h3>

                                 <div class="search-result-excerpt" style="margin-bottom: 15px; line-height: 1.5; color: #666; font-size: 14px; flex-grow: 1;">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15, '...')); ?>
                                 </div>

                                 <a href="<?php echo esc_url(get_permalink()); ?>" class="button-btn ">
                                    <?php echo esc_html__('Devamını Oku', 'artroplasti'); ?>
                                    <span><i class="fas fa-angle-right"></i></span>
                                 </a>
                              </div>
                           </div>
                        </div>
                     <?php endwhile; ?>
                  </div>

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
               </div>
            <?php else : ?>
               <div class="search-no-results" style="background: white; padding: 40px; text-align: center; border-radius: 8px;">
                  <h3 style="margin-bottom: 15px; font-size: 22px; color: #333;">
                     <?php echo esc_html__('Sonuç Bulunamadı', 'artroplasti'); ?>
                  </h3>
                  <p style="margin-bottom: 25px; color: #666;">
                     <?php printf(esc_html__('"%s" için arama sonucu bulunamadı. Lütfen başka bir arama terimi deneyin.', 'artroplasti'), get_search_query()); ?>
                  </p>
                  <a href="<?php echo esc_url(home_url('/')); ?>" class="button-btn">
                     <?php echo esc_html__('Anasayfaya Dön', 'artroplasti'); ?>
                  </a>
               </div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
