<?php
/**
 * Archive template for Webinars
 */

get_header();

$is_en = function_exists('pll_current_language') && pll_current_language() === 'en';
?>

<!-- breadcrumb  start-->
<div class="contact-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="sb-contact-section">
               <nav aria-label="breadcrumb">
                  <h4><?php echo $is_en ? 'Webinars' : 'Webinarlar'; ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo $is_en ? 'Home' : 'Anasayfa'; ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo $is_en ? 'Webinars' : 'Webinarlar'; ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb  end-->

<!-- webinars section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <?php
            // Get all webinar years
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

            // Get selected year from URL parameter or use first year
            $selected_year = isset($_GET['yil']) ? intval($_GET['yil']) : (count($years) > 0 ? $years[0] : null);
            ?>

            <!-- Year Selection -->
            <div class="webinar-years-section mb-5">
               <div class="row">
                  <div class="col-lg-12">
                     <div style="display: none;">
                        <?php foreach ($years as $year) : ?>
                           <a href="<?php echo esc_url(add_query_arg('yil', $year)); ?>"
                              class="button-btn"
                              style="<?php echo ($selected_year == $year) ? 'background: #B81838; color: white;' : 'background: #f5f5f5; color: #333; border: 1px solid #ddd;'; ?> text-decoration: none; display: inline-block; padding: 12px 25px; border-radius: 8px; transition: all 0.3s;">
                              <?php echo esc_html($year . ' ' . ($is_en ? 'EDUCATIONAL WEBINARS' : 'EĞİTİM WEBİNARLARI')); ?>
                           </a>
                        <?php endforeach; ?>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Webinars List -->
            <div class="webinars-list-section">
               <?php
               if ($selected_year) {
                  $webinars_query = new WP_Query(array(
                     'post_type'      => 'webinars',
                     'posts_per_page' => -1,
                     'meta_query'     => array(
                        array(
                           'key'     => 'webinar_year',
                           'value'   => $selected_year,
                           'compare' => '=',
                           'type'    => 'NUMERIC',
                        ),
                     ),
                     'orderby'        => 'date',
                     'order'          => 'DESC',
                  ));

                  if ($webinars_query->have_posts()) {
                     while ($webinars_query->have_posts()) {
                        $webinars_query->the_post();
                     ?>
                     <div class="webinar-item" style="background: white; padding: 25px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid #B81838;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
                           <div style="flex-grow: 1;">
                              <h3 style="font-size: 20px;">
                                 <a href="<?php echo esc_url(get_permalink()); ?>" style="color: #333; text-decoration: none;">
                                    <?php echo esc_html(get_the_title()); ?>
                                 </a>
                              </h3>
                           </div>
                           <a href="<?php echo esc_url(get_permalink()); ?>" class="button-btn py-2 px-4 h-auto line-height-normal" style="display: inline-block; white-space: nowrap;">
                              <?php echo $is_en ? 'Details' : 'Detaylar'; ?>
                           </a>
                        </div>
                     </div>
                     <?php
                     }
                     wp_reset_postdata();
                  } else {
                     echo '<p class="text-center">' . esc_html($is_en ? 'No webinars found for this year.' : 'Bu yılda webinar bulunamadı.') . '</p>';
                  }
               } else {
                  echo '<p class="text-center">' . esc_html($is_en ? 'No webinars found.' : 'Webinar bulunamadı.') . '</p>';
               }
               ?>
            </div>
         </div>

         <div class="col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="webinar-sidebar" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
               <h6 style="margin-bottom: 20px; font-size: 16px; font-weight: 600; text-transform: uppercase;"><?php echo $is_en ? 'Education Years' : 'Eğitim Yılları'; ?></h6>
               <div style="display: flex; flex-direction: column; gap: 8px;">
                  <?php foreach ($years as $year) : ?>
                     <a href="<?php echo esc_url(add_query_arg('yil', $year)); ?>" 
                        class="webinar-year-btn" 
                        style="<?php echo ($selected_year == $year) ? 'background: #B81838; color: white;' : 'background: #f5f5f5; color: #333; border: 1px solid #ddd;'; ?> text-decoration: none; padding: 12px 15px; border-radius: 6px; transition: all 0.3s; text-align: center; font-weight: 500;">
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
