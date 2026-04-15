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
      <div class="congresses-list">
         <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
               <?php
               $course_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
               if (empty($course_image)) {
                  $course_image = artroplasti_default_thumb();
               }
               $course_external_url = get_post_meta(get_the_ID(), 'course_external_url', true);
               $course_date         = get_post_meta(get_the_ID(), 'course_date', true);
               ?>
               <div class="congress-archive-card">
                  <div class="congress-archive-card-inner">
                     <div class="congress-archive-img">
                        <img src="<?php echo esc_url($course_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                     </div>
                     <div class="congress-archive-body">
                        <div class="congress-archive-title-bar">
                           <h2><?php echo esc_html(get_the_title()); ?></h2>
                        </div>
                        <?php if (!empty($course_date)) : ?>
                           <div class="congress-archive-meta">
                              <div class="congress-meta-row">
                                 <i class="far fa-calendar-alt"></i>
                                 <span class="congress-meta-label"><?php echo esc_html__('Tarih', 'artroplasti'); ?></span>
                                 <span class="congress-meta-value"><?php echo esc_html($course_date); ?></span>
                              </div>
                           </div>
                        <?php endif; ?>
                        <div class="congress-archive-links">
                           <?php if (!empty($course_external_url)) : ?>
                              <a href="<?php echo esc_url($course_external_url); ?>" class="congress-link-btn" target="_blank" rel="noopener">
                                 <?php echo esc_html__('Program & Detay', 'artroplasti'); ?>
                              </a>
                           <?php endif; ?>
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
            <p class="text-center"><?php echo esc_html__('Henüz kurs bulunamadı.', 'artroplasti'); ?></p>
         <?php endif; ?>
      </div>
   </div>
</div>

<?php
get_footer();
