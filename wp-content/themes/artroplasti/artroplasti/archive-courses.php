<?php
/**
 * Archive template for Courses
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
                  <h4><?php echo esc_html__('Kurslar', 'artroplasti'); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html__('Kurslar', 'artroplasti'); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- courses section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <?php if (have_posts()) : ?>
               <div class="row">
                  <?php while (have_posts()) : the_post(); ?>
                     <?php
                     $course_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                     if (empty($course_image)) {
                        $course_image = get_template_directory_uri() . '/assets/images/default-blog.jpg';
                     }
                     $course_link = get_post_meta(get_the_ID(), 'course_external_url', true);
                     if (empty($course_link)) {
                        $course_link = get_permalink();
                     }
                     ?>
                     <div class="col-lg-6 col-md-12 mb-5">
                        <div class="course-archive-item" style="background: white; padding: 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                           <div class="row align-items-stretch g-0">
                              <div class="col-lg-5">
                                 <div class="course-archive-image">
                                    <img src="<?php echo esc_url($course_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                 </div>
                              </div>
                              <div class="col-lg-7">
                                 <div class="course-archive-content" style="padding: 25px;">
                                    <h3 style="margin-bottom: 15px; font-size: 20px; color: #333; line-height: 1.4;">
                                       <?php echo esc_html(get_the_title()); ?>
                                    </h3>
                                    
                                    <p style="margin-bottom: 20px; font-size: 14px; color: #666;">
                                       <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15, '...')); ?>
                                    </p>

                                    <div class="course-archive-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                       <a href="<?php echo esc_url($course_link); ?>" class="button-btn h-auto px-4 pt-2 pb-2 line-height-normal" target="_blank" rel="noopener">
                                          <?php echo esc_html__('İncele', 'artroplasti'); ?>
                                       </a>
                                       
                                       <a href="<?php echo esc_url(get_permalink()); ?>" class="button-btn h-auto px-4 py-2 line-height-normal" style="background: #f5f5f5; color: #333; border: 1px solid #ddd;">
                                          <?php echo esc_html__('Detay', 'artroplasti'); ?>
                                       </a>
                                    </div>
                                 </div>
                              </div>
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
            <?php else : ?>
               <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <p class="text-center"><?php echo esc_html__('Henüz kurs bulunamadı.', 'artroplasti'); ?></p>
               </div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
