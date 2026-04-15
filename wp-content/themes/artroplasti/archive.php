<?php
/**
 * Archive template
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
                  <h4><?php the_archive_title(); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php the_archive_title(); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- gallery main section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="row">
               <?php if (have_posts()) : ?>
                  <?php while (have_posts()) : the_post(); ?>
                     <?php
                     $manual_date = get_post_meta(get_the_ID(), 'blog_manual_date', true);
                     $comments_count = get_comments_number();
                     ?>
                     <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="blog-box p-0">
                           <div class="img-icon">
                              <?php if (has_post_thumbnail()) : ?>
                                 <?php
                                 echo wp_get_attachment_image(
                                    get_post_thumbnail_id(get_the_ID()),
                                    'large',
                                    false,
                                    array(
                                       'alt' => get_the_title(),
                                       'loading' => 'lazy',
                                    )
                                 );
                                 ?>
                              <?php else : ?>
                                 <img src="<?php echo esc_url(artroplasti_default_thumb()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                              <?php endif; ?>
                              <div class="img-overlay"></div>
                              <?php if (!empty($manual_date)) : ?>
                                 <p class="text-center"><?php echo esc_html($manual_date); ?></p>
                              <?php else : ?>
                                 <p class="text-center"><?php echo esc_html(get_the_date('d')); ?><br><?php echo esc_html(get_the_date('M')); ?></p>
                              <?php endif; ?>
                           </div>
                           <div class="blog-content">
                              <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                              
                              <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24, '...')); ?></p>
                              <a href="<?php echo esc_url(get_permalink()); ?>" class="r-btn"><?php echo esc_html__('Devamını Oku', 'artroplasti'); ?></a>
                           </div>
                        </div>
                     </div>
                  <?php endwhile; ?>

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
               <?php else : ?>
                  <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                     <p class="text-center"><?php echo esc_html__('Henüz içerik bulunamadı.', 'artroplasti'); ?></p>
                  </div>
               <?php endif; ?>
            </div>
         </div>

      </div>
   </div>
</div>

<?php
get_footer();
