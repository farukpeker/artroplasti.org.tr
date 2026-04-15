<?php
/**
 * Single Event template
 */

get_header();
?>

<!-- breadcrumb start-->
<div class="contact-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="sb-contact-section">
               <nav aria-label="breadcrumb">
                  <h4><?php the_title(); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(get_post_type_archive_link('events')); ?>"><?php echo esc_html__('Etkinlikler', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb end-->

<!-- single event section start-->
<div class="blog-page-main-container float_left ptb-100">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <?php while (have_posts()) : the_post(); ?>
               <?php
               $event_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
               if (empty($event_image)) {
                  $event_image = artroplasti_default_thumb();
               }
               $event_start = get_post_meta(get_the_ID(), 'event_start_date', true);
               $event_end = get_post_meta(get_the_ID(), 'event_end_date', true);
               $event_location = get_post_meta(get_the_ID(), 'event_location', true);
               $event_type = get_post_meta(get_the_ID(), 'event_type', true);
               
               $start_formatted = $event_start ? date_i18n('d F Y', strtotime($event_start)) : '';
               $end_formatted = $event_end ? date_i18n('d F Y', strtotime($event_end)) : '';
               
               // Ayları Türkçeye çevir
               $months_tr = array(
                   'January' => 'Ocak', 'February' => 'Şubat', 'March' => 'Mart',
                   'April' => 'Nisan', 'May' => 'Mayıs', 'June' => 'Haziran',
                   'July' => 'Temmuz', 'August' => 'Ağustos', 'September' => 'Eylül',
                   'October' => 'Ekim', 'November' => 'Kasım', 'December' => 'Aralık'
               );
               
               foreach ($months_tr as $en => $tr) {
                   $start_formatted = str_replace($en, $tr, $start_formatted);
                   $end_formatted = str_replace($en, $tr, $end_formatted);
               }
               ?>
               <div class="event-single-wrapper">
                  <!-- Event Header Image -->
                  <?php if ($event_image) : ?>
                  <div class="event-single-image mb-4">
                     <img src="<?php echo esc_url($event_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="img-fluid" style="border-radius: 8px; width: 100%;">
                  </div>
                  <?php endif; ?>

                  <!-- Event Title -->
                  <h1 style="margin-bottom: 25px; font-size: 32px; color: #333; line-height: 1.3;">
                     <?php echo esc_html(get_the_title()); ?>
                  </h1>

                  <!-- Event Meta Info Box -->
                  <div class="event-meta-box" style="background: #f8f9fa; border-left: 4px solid #e74c3c; padding: 25px; border-radius: 8px; margin-bottom: 30px;">
                     <div class="row">
                        <?php if ($event_start && $event_end) : ?>
                        <div class="col-md-6 mb-3">
                           <div class="meta-item">
                              <i class="far fa-calendar" style="color: #e74c3c; font-size: 20px; margin-right: 10px;"></i>
                              <div style="display: inline-block; vertical-align: top;">
                                 <strong style="display: block; color: #333; margin-bottom: 5px;">Tarih:</strong>
                                 <span style="color: #666;"><?php echo $start_formatted; ?> - <?php echo $end_formatted; ?></span>
                              </div>
                           </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($event_location) : ?>
                        <div class="col-md-6 mb-3">
                           <div class="meta-item">
                              <i class="fas fa-map-marker-alt" style="color: #e74c3c; font-size: 20px; margin-right: 10px;"></i>
                              <div style="display: inline-block; vertical-align: top;">
                                 <strong style="display: block; color: #333; margin-bottom: 5px;">Konum:</strong>
                                 <span style="color: #666;"><?php echo esc_html($event_location); ?></span>
                              </div>
                           </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($event_type) : ?>
                        <div class="col-md-6 mb-3">
                           <div class="meta-item">
                              <i class="fas fa-tag" style="color: #e74c3c; font-size: 20px; margin-right: 10px;"></i>
                              <div style="display: inline-block; vertical-align: top;">
                                 <strong style="display: block; color: #333; margin-bottom: 5px;">Tür:</strong>
                                 <span style="color: #666;"><?php echo esc_html(ucfirst($event_type)); ?></span>
                              </div>
                           </div>
                        </div>
                        <?php endif; ?>
                     </div>
                  </div>

                  <!-- Event Content -->
                  <div class="event-content" style="color: #666; line-height: 1.8; font-size: 16px;">
                     <?php the_content(); ?>
                  </div>

                  <!-- Event Navigation -->
                  <div class="event-navigation" style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #eee;">
                     <div class="row">
                        <div class="col-6">
                           <?php
                           $prev_post = get_previous_post();
                           if ($prev_post) :
                           ?>
                              <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-link prev-event" style="color: #333; text-decoration: none;">
                                 <i class="fa fa-arrow-left"></i> 
                                 <span style="display: block; margin-top: 5px; font-size: 14px; color: #999;">Önceki Etkinlik</span>
                                 <strong style="display: block; margin-top: 5px;"><?php echo esc_html($prev_post->post_title); ?></strong>
                              </a>
                           <?php endif; ?>
                        </div>
                        <div class="col-6 text-end">
                           <?php
                           $next_post = get_next_post();
                           if ($next_post) :
                           ?>
                              <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-link next-event" style="color: #333; text-decoration: none;">
                                 <i class="fas fa-arrow-right"></i>
                                 <span style="display: block; margin-top: 5px; font-size: 14px; color: #999;">Sonraki Etkinlik</span>
                                 <strong style="display: block; margin-top: 5px;"><?php echo esc_html($next_post->post_title); ?></strong>
                              </a>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
            <?php endwhile; ?>
         </div>

         <!-- Sidebar -->
         <div class="col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="blog-sidebar-wrapper">
               <!-- Upcoming Events Widget -->
               <div class="sidebar-widget mb-4">
                  <h4 class="widget-title" style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e74c3c;">
                     <?php _e('Yaklaşan Etkinlikler', 'artroplasti'); ?>
                  </h4>
                  <?php
                  $today = date('Y-m-d');
                  $upcoming_events = new WP_Query(array(
                     'post_type' => 'events',
                     'posts_per_page' => 5,
                     'meta_key' => 'event_start_date',
                     'orderby' => 'meta_value',
                     'order' => 'ASC',
                     'meta_query' => array(
                        array(
                           'key' => 'event_start_date',
                           'value' => $today,
                           'compare' => '>=',
                           'type' => 'DATE'
                        )
                     )
                  ));

                  if ($upcoming_events->have_posts()) :
                     while ($upcoming_events->have_posts()) : $upcoming_events->the_post();
                        $start = get_post_meta(get_the_ID(), 'event_start_date', true);
                        $location = get_post_meta(get_the_ID(), 'event_location', true);
                        ?>
                        <div class="sidebar-event-item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                           <h5 style="margin-bottom: 8px; font-size: 16px;">
                              <a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;">
                                 <?php the_title(); ?>
                              </a>
                           </h5>
                           <p style="margin: 0; color: #999; font-size: 14px;">
                              <i class="far fa-calendar"></i> <?php echo date_i18n('d.m.Y', strtotime($start)); ?>
                              <?php if ($location) : ?>
                                 <br><i class="fas fa-map-marker-alt"></i> <?php echo esc_html($location); ?>
                              <?php endif; ?>
                           </p>
                        </div>
                     <?php
                     endwhile;
                     wp_reset_postdata();
                  else :
                     echo '<p style="color: #999;">' . __('Yaklaşan etkinlik bulunmamaktadır.', 'artroplasti') . '</p>';
                  endif;
                  ?>
                  <a href="<?php echo get_post_type_archive_link('events'); ?>" class="btn btn-sm btn-block" style="background: #e74c3c; color: white; padding: 10px; text-align: center; display: block; border-radius: 5px; text-decoration: none; margin-top: 15px;">
                     <?php _e('Tüm Etkinlikler', 'artroplasti'); ?>
                  </a>
               </div>

               <!-- Calendar Link Widget -->
               <div class="sidebar-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center;">
                  <i class="far fa-calendar" style="font-size: 50px; margin-bottom: 15px; opacity: 0.9;"></i>
                  <h4 style="color: white; margin-bottom: 15px;"><?php _e('Etkinlik Takvimi', 'artroplasti'); ?></h4>
                  <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 20px;">
                     <?php _e('Tüm etkinlikleri takvim görünümünde inceleyin', 'artroplasti'); ?>
                  </p>
                  <?php
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
                  <a href="<?php echo esc_url($calendar_url); ?>" class="btn" style="background: white; color: #667eea; padding: 12px 30px; border-radius: 5px; text-decoration: none; display: inline-block; font-weight: 600;">
                     <?php _e('Takvimi Görüntüle', 'artroplasti'); ?>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- single event section end-->

<?php get_footer(); ?>
