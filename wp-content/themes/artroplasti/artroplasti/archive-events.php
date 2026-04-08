<?php
/**
 * Archive template for Events
 */

get_header();

// Yıl filtresi
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
?>

<!-- breadcrumb start-->
<div class="contact-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="sb-contact-section">
               <nav aria-label="breadcrumb">
                  <h4><?php echo esc_html__('Etkinlikler', 'artroplasti'); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html__('Etkinlikler', 'artroplasti'); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb end-->

<!-- events section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <!-- Filtre ve Takvim Butonu -->
      <div class="events-filter-header mb-4">
         <div class="row align-items-center">
            <div class="col-lg-6 col-md-6">
               <div class="year-filter">
                  <label for="year-filter" style="margin-right: 10px; font-weight: 600;"><?php _e('Yıl:', 'artroplasti'); ?></label>
                  <select id="year-filter" class="form-control" style="width: auto; display: inline-block;" onchange="window.location.href='?year='+this.value">
                     <?php for ($y = 2025; $y <= 2030; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php selected($selected_year, $y); ?>><?php echo $y; ?></option>
                     <?php endfor; ?>
                  </select>
               </div>
            </div>
            <div class="col-lg-6 col-md-6 text-end">
               <?php
               // Takvim sayfasının URL'sini bul
               $calendar_page = get_pages(array(
                   'meta_key' => '_wp_page_template',
                   'meta_value' => 'template-calendar.php'
               ));
               if (!empty($calendar_page)) {
                   $calendar_url = get_permalink($calendar_page[0]->ID);
               } else {
                   $calendar_url = home_url('/etkinlik-takvimi/');
               }
               ?>
               <a href="<?php echo esc_url($calendar_url); ?>" class="btn btn-primary">
                  <i class="fas fa-calendar"></i> <?php _e('Takvim Görünümü', 'artroplasti'); ?>
               </a>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-lg-12">
            <?php
            // Seçilen yıla göre etkinlikleri getir
            $args = array(
                'post_type' => 'events',
                'posts_per_page' => -1,
                'meta_key' => 'event_start_date',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'event_start_date',
                        'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                        'compare' => 'BETWEEN',
                        'type' => 'DATE'
                    )
                )
            );

            $events_query = new WP_Query($args);

            if ($events_query->have_posts()) :
                // Etkinlikleri aya göre grupla
                $events_by_month = array();
                while ($events_query->have_posts()) : $events_query->the_post();
                    $start_date = get_post_meta(get_the_ID(), 'event_start_date', true);
                    $month = date('n', strtotime($start_date)); // 1-12
                    
                    if (!isset($events_by_month[$month])) {
                        $events_by_month[$month] = array();
                    }
                    $events_by_month[$month][] = get_the_ID();
                endwhile;
                wp_reset_postdata();

                // Ay adları
                $months_tr = array(
                    1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
                    5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
                    9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
                );

                // Her ay için etkinlikleri göster
                ksort($events_by_month);
                foreach ($events_by_month as $month => $event_ids) :
                    ?>
                    <div class="month-events-section mb-5">
                        <h3 class="month-title" style="border-bottom: 3px solid #e74c3c; padding-bottom: 10px; margin-bottom: 30px;">
                            <?php echo $months_tr[$month] . ' ' . $selected_year; ?>
                        </h3>
                        <div class="events-list">
                            <?php foreach ($event_ids as $event_id) :
                                $event_start = get_post_meta($event_id, 'event_start_date', true);
                                $event_end = get_post_meta($event_id, 'event_end_date', true);
                                $event_location = get_post_meta($event_id, 'event_location', true);
                                $event_type = get_post_meta($event_id, 'event_type', true);
                                $event_image = get_the_post_thumbnail_url($event_id, 'medium');
                                
                                if (empty($event_image)) {
                                    $event_image = get_template_directory_uri() . '/assets/images/default-blog.jpg';
                                }

                                $start_formatted = date_i18n('d.m.Y', strtotime($event_start));
                                $end_formatted = date_i18n('d.m.Y', strtotime($event_end));
                                ?>
                                <div class="event-archive-item mb-4" style="background: white; padding: 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                                    <div class="row align-items-stretch g-0">
                                        <div class="col-lg-3 col-md-4">
                                            <div class="event-image" style="height: 100%; min-height: 200px; background-image: url('<?php echo esc_url($event_image); ?>'); background-size: cover; background-position: center;">
                                            </div>
                                        </div>
                                        <div class="col-lg-9 col-md-8">
                                            <div class="event-content" style="padding: 25px;">
                                                <div class="event-meta mb-3">
                                                    <span class="event-date" style="display: inline-block; background: #e74c3c; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; margin-right: 10px;">
                                                        <i class="fas fa-calendar"></i> <?php echo $start_formatted; ?> - <?php echo $end_formatted; ?>
                                                    </span>
                                                    <?php if ($event_location) : ?>
                                                        <span class="event-location" style="display: inline-block; color: #555; font-size: 14px;">
                                                            <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($event_location); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($event_type) : ?>
                                                        <span class="event-type" style="display: inline-block; margin-left: 10px; padding: 5px 15px; background: #f5f5f5; border-radius: 20px; font-size: 13px;">
                                                            <?php echo esc_html(ucfirst($event_type)); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4 style="margin-bottom: 15px; font-size: 22px;">
                                                    <a href="<?php echo get_permalink($event_id); ?>" style="color: #333; text-decoration: none;">
                                                        <?php echo get_the_title($event_id); ?>
                                                    </a>
                                                </h4>
                                                
                                                <div class="event-link mt-3">
                                                    <a href="<?php echo get_permalink($event_id); ?>" class="btn btn-sm" style="background: #e74c3c; color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                                                        <?php _e('Detaylı Bilgi', 'artroplasti'); ?> <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else : ?>
                <div class="no-events-found" style="text-align: center; padding: 60px 20px; background: #f9f9f9; border-radius: 8px;">
                    <i class="fa fa-calendar-times-o" style="font-size: 60px; color: #ccc; margin-bottom: 20px;"></i>
                    <h3><?php echo sprintf(__('%s yılı için etkinlik bulunamadı', 'artroplasti'), $selected_year); ?></h3>
                    <p style="color: #666; margin-top: 15px;"><?php _e('Başka bir yıl seçmeyi deneyin.', 'artroplasti'); ?></p>
                </div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>
<!-- events section end-->

<?php get_footer(); ?>
