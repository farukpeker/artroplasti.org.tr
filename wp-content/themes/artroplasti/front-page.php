<?php
/**
 * Template Name: Anasayfa
 */

get_header();
?>

<main id="main-content" class="site-main front-page">
   <!-- banner section start -->
   <div class="banner-section-wrapper float_left">
      <div class="banner-slider owl-carousel">
         <?php
         $slider_query = new WP_Query(array(
            'post_type'      => 'banner_slide',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
         ));

         if ($slider_query->have_posts()) :
            while ($slider_query->have_posts()) :
               $slider_query->the_post();
               $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
               $button_url = get_post_meta(get_the_ID(), 'banner_button_url', true);
               $button_text = get_post_meta(get_the_ID(), 'banner_button_text', true);
               $description = get_the_excerpt();

               if (empty($description)) {
                  $description = wp_trim_words(strip_shortcodes(get_the_content()), 25);
               }

               if (empty($button_text)) {
                  $button_text = __('Devamı', 'artroplasti');
               }
         ?>
         <div class="banner-slide">
            <?php if (!empty($image_url)) : ?>
               <img class="banner-slide-image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
            <?php endif; ?>
            <div class="container banner-slide-content">
               <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                     <div class="sb-banner-section">
                        <?php if (!empty($button_url)) : ?>
                        <a href="<?php echo esc_url($button_url); ?>" class="button-btn mt-4">
                           <?php echo esc_html($button_text); ?>
                           <span><i class="fas fa-angle-double-right"></i></span>
                        </a>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php
            endwhile;
            wp_reset_postdata();
         endif;
         ?>
      </div>
   </div>

   <!-- blog section start -->
   <div class="blog-main-wrapper">
      <div class="container">
         <div class="row d-flex">
            <div class="col-lg-12 col-md-12 col sm-12 col-12">
               <div class="sb-blog-main-section mb-4">
                  <h5 class="home1-section-heading2 mb-2"><?php echo esc_html__('Haberler, Duyurular ve Güncellemeler', 'artroplasti'); ?></h5>
                  <p><?php echo esc_html__('Artroplasti Derneği\'nin en son haberlerini ve duyurularını takip edin.', 'artroplasti'); ?></p>
               </div>
            </div>
            <?php
            $blog_query = new WP_Query(array(
               'post_type'      => 'post',
               'posts_per_page' => 6,
               'orderby'        => 'date',
               'order'          => 'DESC',
            ));

            if ($blog_query->have_posts()) :
               while ($blog_query->have_posts()) :
                  $blog_query->the_post();
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12 col-12 d-flex">
               <div class="blog-box p-2 mb-4 box-shadow rounded border ">
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
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/default-blog.jpg'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                     <?php endif; ?>
                     <div class="img-overlay"></div>
                     <?php
                     $manual_date = get_post_meta(get_the_ID(), 'blog_manual_date', true);
                     if (!empty($manual_date)) {
                        echo '<p class="text-center">' . esc_html($manual_date) . '</p>';
                     } else {
                        echo '<p class="text-center">' . get_the_date('d') . '<br>' . get_the_date('M') . '</p>';
                     }
                     ?>
                  </div>
                  <div class="blog-content px-2 pb-2">
                     <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                     <p><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                     <a href="<?php echo esc_url(get_permalink()); ?>" class="r-btn"><?php echo esc_html__('Devamını Oku', 'artroplasti'); ?></a>
                  </div>
               </div>
            </div>
            <?php
               endwhile;
               wp_reset_postdata();
            else :
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
               <p class="text-center"><?php echo esc_html__('Henüz blog yazısı eklenmemiş.', 'artroplasti'); ?></p>
            </div>
            <?php endif; ?>
         </div>
         <div class="row mt-4">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">
               <a href="<?php echo esc_url(home_url('/haberler')); ?>" class="button-btn">
                  <?php echo esc_html__('Tüm Arşivi Görüntüle', 'artroplasti'); ?>
                  <span><i class="fas fa-angle-double-right"></i></span>
               </a>
            </div>
         </div>
      </div>
   </div>

   <div class="congress-main-wrapper">
      <div class="container">
         <?php
         $congress_query = new WP_Query(array(
            'post_type'      => 'congresses',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
         ));

         if ($congress_query->have_posts()) :
            while ($congress_query->have_posts()) :
               $congress_query->the_post();
               $congress_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
               $congress_date = get_post_meta(get_the_ID(), 'congress_date', true);
               $congress_location = get_post_meta(get_the_ID(), 'congress_location', true);
               $congress_site_url = get_post_meta(get_the_ID(), 'congress_site_url', true);
               $congress_program_url = get_post_meta(get_the_ID(), 'congress_program_url', true);
               $congress_site_label = get_post_meta(get_the_ID(), 'congress_site_label', true);
               $congress_program_label = get_post_meta(get_the_ID(), 'congress_program_label', true);
               $congress_website_url = get_post_meta(get_the_ID(), 'congress_website_url', true);
               $congress_website_label = get_post_meta(get_the_ID(), 'congress_website_label', true);

               if (empty($congress_site_label)) {
                  $congress_site_label = __('Toplantı Web Sitesi (Yakında)', 'artroplasti');
               }

               if (empty($congress_program_label)) {
                  $congress_program_label = __('Kongre Programı (Yakında)', 'artroplasti');
               }

               if (empty($congress_website_label)) {
                  $congress_website_label = __('Web sitesi', 'artroplasti');
               }
         ?>
         <div class="row align-items-center">
            <div class="col-md-12">
                  <div class="congress-title-bar mb-4 text-center">
                     <h4><?php echo esc_html__('Gelecek Kongremiz', 'artroplasti'); ?></h4>
                  </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
               <div class="congress-content mt-4">
                  
                  <h3 class="congress-title"><?php echo esc_html(get_the_title()); ?></h3>
                  <?php if (!empty($congress_date)) : ?>
                     <p class="congress-meta"><strong><?php echo esc_html__('Tarih:', 'artroplasti'); ?></strong> <?php echo esc_html($congress_date); ?></p>
                  <?php endif; ?>
                  <?php if (!empty($congress_location)) : ?>
                     <p class="congress-meta"><strong><?php echo esc_html__('Yer:', 'artroplasti'); ?></strong> <?php echo esc_html($congress_location); ?></p>
                  <?php endif; ?>
                  <div class="congress-actions">
                     <?php if (!empty($congress_site_url)) : ?>
                        <a href="<?php echo esc_url($congress_site_url); ?>" class="button-btn" target="_blank" rel="noopener">
                           <?php echo esc_html($congress_site_label); ?>
                        </a>
                     <?php else : ?>
                        <span class="button-btn disabled"><?php echo esc_html($congress_site_label); ?></span>
                     <?php endif; ?>

                     <?php if (!empty($congress_program_url)) : ?>
                        <a href="<?php echo esc_url($congress_program_url); ?>" class="button-btn" target="_blank" rel="noopener">
                           <?php echo esc_html($congress_program_label); ?>
                        </a>
                     <?php else : ?>
                        <span class="button-btn disabled"><?php echo esc_html($congress_program_label); ?></span>
                     <?php endif; ?>

                     <?php if (!empty($congress_website_url)) : ?>
                        <a href="<?php echo esc_url($congress_website_url); ?>" class="button-btn" target="_blank" rel="noopener">
                           <?php echo esc_html($congress_website_label); ?>
                        </a>
                     <?php else : ?>
                        <span class="button-btn disabled"><?php echo esc_html($congress_website_label); ?></span>
                     <?php endif; ?>
                  </div>
                  <div class="congress-description">
                     <?php the_excerpt(); ?>
                  </div>
               </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
               <div class="congress-image">
                  <?php if (!empty($congress_image)) : ?>
                     <img src="<?php echo esc_url($congress_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                  <?php endif; ?>
               </div>
            </div>
         </div>
         <?php
            endwhile;
            wp_reset_postdata();
         endif;
         ?>
      </div>
   </div>
   <!-- Upcoming Events Section Start -->
   <div class="events-main-wrapper float_left service-section3 py-5">
      <div class="container">
         <div class="sb-about-section">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <?php
                  // Takvim verilerini hazırla
                  $current_month = intval(date('m'));
                  $current_year = intval(date('Y'));
                  
                  // Ay adları
                  $months_tr = array(
                     1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
                     5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
                     9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
                  );
                  
                  // Gün adları kısaltmaları
                  $days_tr = array('Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz');
                  
                  // Takvim hesaplamaları
                  $first_day_of_month = mktime(0, 0, 0, $current_month, 1, $current_year);
                  $days_in_month = date('t', $first_day_of_month);
                  $first_day_weekday = date('N', $first_day_of_month);
                  
                  // Bu ay için etkinlikleri çek
                  $start_date = sprintf('%04d-%02d-01', $current_year, $current_month);
                  $end_date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $days_in_month);
                  
                  $events_query = new WP_Query(array(
                     'post_type' => 'events',
                     'posts_per_page' => -1,
                     'meta_query' => array(
                        'relation' => 'OR',
                        array(
                           'key' => 'event_start_date',
                           'value' => array($start_date, $end_date),
                           'compare' => 'BETWEEN',
                           'type' => 'DATE'
                        ),
                        array(
                           'relation' => 'AND',
                           array(
                              'key' => 'event_start_date',
                              'value' => $start_date,
                              'compare' => '<=',
                              'type' => 'DATE'
                           ),
                           array(
                              'key' => 'event_end_date',
                              'value' => $start_date,
                              'compare' => '>=',
                              'type' => 'DATE'
                           )
                        )
                     )
                  ));
                  
                  // Etkinlikleri tarihe göre grupla
                  $events_by_date = array();
                  if ($events_query->have_posts()) {
                     while ($events_query->have_posts()) {
                        $events_query->the_post();
                        $event_start = get_post_meta(get_the_ID(), 'event_start_date', true);
                        $event_end = get_post_meta(get_the_ID(), 'event_end_date', true);
                        
                        $start = strtotime($event_start);
                        $end = strtotime($event_end);
                        
                        for ($date = $start; $date <= $end; $date = strtotime('+1 day', $date)) {
                           $day = date('j', $date);
                           $month = date('n', $date);
                           $year = date('Y', $date);
                           
                           if ($month == $current_month && $year == $current_year) {
                              if (!isset($events_by_date[$day])) {
                                 $events_by_date[$day] = array();
                              }
                              $events_by_date[$day][] = array(
                                 'title' => get_the_title(),
                                 'url' => get_permalink()
                              );
                           }
                        }
                     }
                     wp_reset_postdata();
                  }
                  ?>
                  
                  <!-- Takvim Başlığı ve Yıl Seçici -->
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                     <h5 style="font-size: 28px; font-weight: 700; color: #333; margin: 0;">
                        <?php echo esc_html__('Etkinlik Takvimi', 'artroplasti'); ?>
                     </h5>
                     
                     <div class="year-selector" style="min-width: 150px;">
                        <select id="year-select" onchange="var currentMonth = new URLSearchParams(window.location.search).get('month') || '<?php echo $current_month; ?>'; window.location.href = '<?php echo esc_url(home_url('/etkinlik-takvimi')); ?>?month=' + currentMonth + '&year=' + this.value;" style="padding: 8px 12px; border: 2px solid #ddd; border-radius: 5px; font-size: 14px; cursor: pointer; width: 100%;">
                           <?php for ($y = 2025; $y <= 2030; $y++): ?>
                              <option value="<?php echo $y; ?>" <?php selected($current_year, $y); ?>><?php echo $y; ?></option>
                           <?php endfor; ?>
                        </select>
                     </div>
                  </div>
                  
                  <!-- Ay Seçme Tabları -->
                  <div class="month-tabs" style="display: flex; gap:5px; flex-wrap: wrap; justify-content: center;">
                        <?php foreach ($months_tr as $month_num => $month_name): 
                           $month_url = add_query_arg(array('month' => $month_num, 'year' => $current_year), home_url('/etkinlik-takvimi'));
                        ?>
                           <a href="<?php echo esc_url($month_url); ?>" 
                              class="month-tab <?php echo $month_num == $current_month ? 'active' : ''; ?>"
                              style="padding: 8px 10px; border-radius: 5px; background: <?php echo $month_num == $current_month ? '#e74c3c' : '#f0f0f0'; ?>; color: <?php echo $month_num == $current_month ? 'white' : '#333'; ?>; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.3s; margin-top:0">
                              <?php echo $month_name; ?>
                           </a>
                        <?php endforeach; ?>
                     </div>
                  
                  <!-- Takvim Tablosu -->
                  <table class="event-calendar-homepage" style="width: 100%; border-collapse: collapse; background: white;">
                     <thead>
                        <tr>
                           <?php foreach ($days_tr as $day): ?>
                              <th style="padding: 15px; background: #f8f9fa; border: 1px solid #ddd; text-align: center; font-weight: 600; color: #333;">
                                 <?php echo $day; ?>
                              </th>
                           <?php endforeach; ?>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $day_count = 1;
                        $calendar_started = false;
                        
                        for ($week = 0; $week < 6; $week++):
                           if ($day_count > $days_in_month) break;
                           ?>
                           <tr>
                              <?php for ($weekday = 1; $weekday <= 7; $weekday++): ?>
                                 <td style="padding: 12px; border: 1px solid #ddd; height: 100px; vertical-align: top; background: <?php echo (($week == 0 && $weekday >= $first_day_weekday) || ($calendar_started && $day_count <= $days_in_month)) && isset($events_by_date[$day_count]) ? '#fff5f5' : '#ffffff'; ?>;">
                                    <?php
                                    if (($week == 0 && $weekday >= $first_day_weekday) || ($calendar_started && $day_count <= $days_in_month)) {
                                       $calendar_started = true;
                                       ?>
                                       <div style="font-weight: 600; color: #333; margin-bottom: 8px; font-size: 14px;">
                                          <?php echo $day_count; ?>
                                       </div>
                                       
                                       <?php
                                       if (isset($events_by_date[$day_count]) && is_array($events_by_date[$day_count])) {
                                          foreach ($events_by_date[$day_count] as $event) {
                                             ?>
                                             <a href="<?php echo esc_url($event['url']); ?>" style="display: block; font-size: 11px; color: white; background: #e74c3c; padding: 4px 6px; margin-bottom: 4px; border-radius: 3px; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo esc_html($event['title']); ?>
                                             </a>
                                             <?php
                                          }
                                       }
                                       $day_count++;
                                    }
                                    ?>
                                 </td>
                              <?php endfor; ?>
                           </tr>
                        <?php endfor; ?>
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="row mt-4">
               <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                  <a href="<?php echo esc_url(home_url('/etkinlik-takvimi')); ?>" class="button-btn" style="color:white;">
                     <?php echo esc_html__('Tam Takvimi Gör', 'artroplasti'); ?>
                     <span><i class="fas fa-calendar-alt"></i></span>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Upcoming Events Section End -->
   <div class="services-main-wrapper service-slider-wrappe3 float_left courses-section">
      <div class="container">
         <div class="row">
            <div class="col-lg-12 col-md-12">
               <div class="sb-service-section">
                  <div class="d-xl-flex d-lg-flex justify-content-between d-md-block d-block align-items-center">
                     <div>
                        <h5 class="home1-section-heading2 text-start"><?php echo esc_html__('Kurslar', 'artroplasti'); ?></h5>
                     </div>
                     <div class="mt-3 mt-lg-0">
                        <a href="<?php echo esc_url(get_post_type_archive_link('courses')); ?>" class="button-btn">
                           <?php echo esc_html__('Tüm Kurslar', 'artroplasti'); ?>
                           <span><i class="fas fa-angle-double-right"></i></span>
                        </a>
                     </div>
                  </div>
                  <div class="slider-service-section">
                     <div class="owl-carousel owl-theme">
                        <?php
                        $courses_query = new WP_Query(array(
                           'post_type'      => 'courses',
                           'posts_per_page' => 8,
                           'orderby'        => 'date',
                           'order'          => 'DESC',
                        ));

                        if ($courses_query->have_posts()) :
                           while ($courses_query->have_posts()) :
                              $courses_query->the_post();
                              $course_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                              $course_link = get_post_meta(get_the_ID(), 'course_external_url', true);
                              if (empty($course_link)) {
                                 $course_link = get_permalink();
                              }
                        ?>
                        <div class="item">
                           <div class="slider-content3 course-card">
                              <div class="course-image">
                                 <?php if (!empty($course_image)) : ?>
                                    <img src="<?php echo esc_url($course_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                 <?php endif; ?>
                              </div>
                              <div>
                                    <h6 class="pt-4"><?php echo esc_html(get_the_title()); ?></h6>
                                        <p class="text-start"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20, '...')); ?></p>
                                    <a href="<?php echo esc_url($course_link); ?>" class="text-color-change3 mt-2 d-inline-block" target="_blank" rel="noopener">
                                        <?php echo esc_html__('İncele', 'artroplasti'); ?> &gt;
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                           endwhile;
                           wp_reset_postdata();
                        else :
                        ?>
                        <div class="item">
                           <div class="slider-content3 course-card">
                              <p class="text-start"><?php echo esc_html__('Henüz kurs eklenmemiş.', 'artroplasti'); ?></p>
                           </div>
                        </div>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="about-main-wrapper float_left service-section3 featured-slider-section">
      <div class="container">
         <div class="sb-about-section">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                  <div class="sb-blog-main-section text-center">
                     <h4 class="home1-section-heading2 mb-2"><?php echo esc_html__('Sizin için Seçtiklerimiz', 'artroplasti'); ?></h4>
                  </div>
               </div>
            </div>
            <div class="slider-service-section featured-slider-wrapper plr-50">
               <div class="owl-carousel owl-theme featured-items-carousel">
                  <?php
                  $featured_query = new WP_Query(array(
                     'post_type'      => 'featured_items',
                     'posts_per_page' => 6,
                     'orderby'        => 'menu_order',
                     'order'          => 'ASC',
                  ));

                  if ($featured_query->have_posts()) :
                     while ($featured_query->have_posts()) :
                        $featured_query->the_post();
                        $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        $link_type = get_post_meta(get_the_ID(), 'featured_link_type', true);
                        $internal_page = get_post_meta(get_the_ID(), 'featured_internal_page', true);
                        $external_url = get_post_meta(get_the_ID(), 'featured_external_url', true);

                        if ($link_type === 'external' && !empty($external_url)) {
                           $item_link = $external_url;
                           $target = ' target="_blank" rel="noopener"';
                        } elseif (!empty($internal_page)) {
                           $item_link = get_permalink($internal_page);
                           $target = '';
                        } else {
                           $item_link = '#';
                           $target = '';
                        }
                  ?>
                  <div class="item">
                     <div class="slider-content3 featured-card">
                        <div class="featured-card-inner d-flex align-items-center">
                           <?php if (!empty($featured_image)) : ?>
                           <div class="icon flex-shrink-0 featured-card-icon">
                              <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                           </div>
                           <?php endif; ?>
                           <div class="content flex-grow-1 featured-card-content">
                              <h5><?php echo esc_html(get_the_title()); ?></h5>
                              <a href="<?php echo esc_url($item_link); ?>"<?php echo $target; ?>><?php echo esc_html__('İncele', 'artroplasti'); ?> +</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php
                     endwhile;
                     wp_reset_postdata();
                  else :
                  ?>
                  <div class="item">
                     <div class="slider-content3 featured-card">
                        <p class="mb-0 text-start"><?php echo esc_html__('Henüz içerik eklenmemiş.', 'artroplasti'); ?></p>
                     </div>
                  </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   </div>

</main>

<?php
get_footer();
