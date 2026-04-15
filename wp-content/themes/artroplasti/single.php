<?php
/**
 * Single post template
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
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/haberler')); ?>"><?php echo esc_html__('Haberler', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- single post section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <?php while (have_posts()) : the_post(); ?>
               <?php
               $blog_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
               if (empty($blog_image)) {
                  $blog_image = artroplasti_default_thumb();
               }
               $manual_date = get_post_meta(get_the_ID(), 'blog_manual_date', true);
               $comments_count = get_comments_number();
               ?>
               <div class="blog-single-main-page">
                  <div class="blog-box p-0">
                     <div class="img-icon">
                        <img src="<?php echo esc_url($blog_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php if (!empty($manual_date)) : ?>
                           <p class="text-center"><?php echo esc_html($manual_date); ?></p>
                        <?php else : ?>
                           <p class="text-center"><?php echo esc_html(get_the_date('d')); ?><br><?php echo esc_html(get_the_date('M')); ?></p>
                        <?php endif; ?>
                     </div>
                     <div class="blog-content">
                        <h2 class="p-0 h5"><?php echo esc_html(get_the_title()); ?></h2>
                        
                        <div class="post-body">
                           <?php the_content(); ?>
                        </div>

                        <?php
                        $pdf_url = get_post_meta(get_the_ID(), 'blog_pdf_url', true);
                        if (!empty($pdf_url)) : ?>
                        <div class="post-pdf-download" style="margin-top: 24px;">
                           <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener" class="btn btn-danger" style="display:inline-flex; align-items:center; gap:8px; background:#B81838; color:#fff; padding:10px 22px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;">
                              <i class="fas fa-file-pdf"></i>
                              <?php echo esc_html__('Detaylı Bilgi İçin', 'artroplasti'); ?>
                           </a>
                        </div>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            <?php endwhile; ?>
         </div>

         <div class="col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="blog-right-sidebar resp-30">
               <div class="form-section m-0">
                  <h6><?php echo esc_html__('Arama', 'artroplasti'); ?></h6>
                  <section>
                     <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-group" style="display: flex; gap: 8px;">
                           <input type="search" class="form-control" placeholder="<?php echo esc_attr__('Arama yap', 'artroplasti'); ?>" value="<?php echo get_search_query(); ?>" name="s" aria-describedby="basic-addon2" style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                           <button class="input-group-text" id="basic-addon2" type="submit" style="background: white; border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; cursor: pointer; transition: all 0.3s; color: #B81838;">
                              <i class="fas fa-paper-plane"></i>
                           </button>
                        </div>
                     </form>
                  </section>
               </div>

                    <div class="form-section">
                        <h6><?php echo esc_html__('Son Haberler', 'artroplasti'); ?></h6>
                        <section>
                            <?php
                            $recent_posts = new WP_Query(array(
                                'post_type'      => 'post',
                                'posts_per_page' => 6,
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                            ));

                            if ($recent_posts->have_posts()) :
                                while ($recent_posts->have_posts()) :
                                    $recent_posts->the_post();
                                    $recent_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                                    if (empty($recent_image)) {
                                        $recent_image = artroplasti_default_thumb();
                                    }
                            ?>
                            <div class="post-main-container hr-line">
                                <div class="post-image me-3">
                                    <img src="<?php echo esc_url($recent_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 90px; height: 90px; object-fit: cover;">
                                </div>
                                <div class="post-container">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" class="h6"><?php echo esc_html(get_the_title()); ?></a>
                                    <p><?php echo esc_html(get_the_date('d M Y')); ?></p>
                                </div>
                            </div>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </section>
                    </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
get_footer();
