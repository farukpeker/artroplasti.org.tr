<?php
/**
 * Single Course template
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
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(get_post_type_archive_link('courses')); ?>"><?php echo esc_html__('Kurslar', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- single course section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <?php while (have_posts()) : the_post(); ?>
               <?php
               $course_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
               if (empty($course_image)) {
                  $course_image = artroplasti_default_thumb();
               }
               $course_link = get_post_meta(get_the_ID(), 'course_external_url', true);
               if (empty($course_link)) {
                  $course_link = get_permalink();
               }
               ?>
               <div class="course-single-wrapper">
                  <div class="row align-items-center g-4 mb-5">
                     <div class="col-lg-4 col-md-5 col-sm-12">
                        <div class="course-single-image">
                           <img src="<?php echo esc_url($course_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="img-fluid" style="border-radius: 8px; width: 100%;">
                        </div>
                     </div>
                     <div class="col-lg-8 col-md-7 col-sm-12">
                        <div class="course-single-info" style="padding: 20px;">
                           <h1 style="margin-bottom: 15px; font-size: 28px; color: #333; line-height: 1.3;">
                              <?php echo esc_html(get_the_title()); ?>
                           </h1>

                           <div class="course-single-meta mb-4">
                              <?php 
                              $course_date = get_post_meta(get_the_ID(), 'course_date', true);
                              if (!empty($course_date)) :
                              ?>
                              <p style="color: #999; font-size: 14px; margin: 0;">
                                 <i class="fas fa-calendar" style="margin-right: 8px;"></i>
                                 <?php echo esc_html($course_date); ?>
                              </p>
                              <?php endif; ?>
                           </div>

                           <div class="course-excerpt mb-4">
                              <p style="color: #666; line-height: 1.6;">
                               <?php the_content(); ?>
                                </p>
                           </div>

                           <div class="course-single-actions">
                              <a href="<?php echo esc_url($course_link); ?>" class="button-btn" target="_blank" rel="noopener">
                                 <?php echo esc_html__('İncele', 'artroplasti'); ?>
                                 <span><i class="fas fa-angle-double-right"></i></span>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>

                  <?php
                  $prev_course = get_previous_post();
                  $next_course = get_next_post();
                  if ($prev_course || $next_course) :
                  ?>
                  <div class="course-navigation mt-5 pt-5" style="border-top: 1px solid #eee;">
                     <div class="row">
                        <div class="col-md-6">
                           <?php if ($prev_course) : ?>
                              <a href="<?php echo esc_url(get_permalink($prev_course)); ?>" class="prev-course" style="display: inline-block; padding: 10px 20px; background: #f5f5f5; border-radius: 5px; text-decoration: none; color: #333;">
                                 <i class="fas fa-angle-left"></i> <?php echo esc_html($prev_course->post_title); ?>
                              </a>
                           <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                           <?php if ($next_course) : ?>
                              <a href="<?php echo esc_url(get_permalink($next_course)); ?>" class="next-course" style="display: inline-block; padding: 10px 20px; background: #f5f5f5; border-radius: 5px; text-decoration: none; color: #333;">
                                 <?php echo esc_html($next_course->post_title); ?> <i class="fas fa-angle-right"></i>
                              </a>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
                  <?php endif; ?>
               </div>
            <?php endwhile; ?>
         </div>

         <div class="col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="course-sidebar">
               <!-- Recent Courses -->
               <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 25px;">
                  <h5 style="margin-bottom: 15px; font-weight: 600;">
                     <?php echo esc_html__('Diğer Kurslar', 'artroplasti'); ?>
                  </h5>
                  <ul style="list-style: none; padding: 0; margin: 0;">
                     <?php
                     $recent_courses = new WP_Query(array(
                        'post_type'      => 'courses',
                        'posts_per_page' => 5,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                        'post__not_in'   => array(get_the_ID()),
                     ));

                     if ($recent_courses->have_posts()) :
                        while ($recent_courses->have_posts()) :
                           $recent_courses->the_post();
                           $thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                     ?>
                     <li style="display: flex; gap: 12px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                        <?php if (!empty($thumb)) : ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; flex-shrink: 0;">
                        <?php endif; ?>
                        <div style="flex-grow: 1;">
                           <a href="<?php echo esc_url(get_permalink()); ?>" style="color: #333; text-decoration: none; display: block; font-weight: 500; line-height: 1.4;">
                              <?php echo esc_html(get_the_title()); ?>
                           </a>
                        </div>
                     </li>
                     <?php
                        endwhile;
                        wp_reset_postdata();
                     endif;
                     ?>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
