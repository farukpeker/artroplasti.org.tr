<?php
/**
 * Template Name: Etkinlik Takvimi
 * Description: Aylık etkinlik takvimi görünümü
 */

// Query string'leri handle et - 404 engelle
if ( isset( $_GET['month'] ) || isset( $_GET['year'] ) ) {
    status_header( 200 );
    global $wp_query;
    $wp_query->is_404 = false;
}

get_header();

// Mevcut sayfanın URL'sini al
$current_page_url = get_permalink();

$current_month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$current_year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

// Önceki ve sonraki ay için linkler
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Ay adları
$months_tr = array(
    1 => __('Ocak', 'artroplasti'), 2 => __('Şubat', 'artroplasti'), 3 => __('Mart', 'artroplasti'), 4 => __('Nisan', 'artroplasti'),
    5 => __('Mayıs', 'artroplasti'), 6 => __('Haziran', 'artroplasti'), 7 => __('Temmuz', 'artroplasti'), 8 => __('Ağustos', 'artroplasti'),
    9 => __('Eylül', 'artroplasti'), 10 => __('Ekim', 'artroplasti'), 11 => __('Kasım', 'artroplasti'), 12 => __('Aralık', 'artroplasti')
);

// Gün adları kısaltmaları
$days_tr = array(__('Pzt', 'artroplasti'), __('Sal', 'artroplasti'), __('Çar', 'artroplasti'), __('Per', 'artroplasti'), __('Cum', 'artroplasti'), __('Cmt', 'artroplasti'), __('Paz', 'artroplasti'));

// Ayın ilk ve son günü
$first_day_of_month = mktime(0, 0, 0, $current_month, 1, $current_year);
$days_in_month = date('t', $first_day_of_month);
$first_day_weekday = date('N', $first_day_of_month); // 1=Pazartesi, 7=Pazar

// Seçili ay için etkinlikleri çek
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

// Tüm yıl için etkinlikleri çek
$year_start_date = sprintf('%04d-01-01', $current_year);
$year_end_date = sprintf('%04d-12-31', $current_year);

$year_events_query = new WP_Query(array(
    'post_type' => 'events',
    'posts_per_page' => -1,
    'meta_key' => 'event_start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'event_start_date',
            'value' => array($year_start_date, $year_end_date),
            'compare' => 'BETWEEN',
            'type' => 'DATE'
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
        $event_location = get_post_meta(get_the_ID(), 'event_location', true);
        
        // Etkinlik bu ayın içinde mi kontrol et
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
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'location' => $event_location,
                    'start' => $event_start,
                    'end' => $event_end,
                    'url' => get_permalink()
                );
            }
        }
    }
    wp_reset_postdata();
}
?>

<!-- breadcrumb start -->
<div class="contact-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="sb-contact-section">
               <nav aria-label="breadcrumb">
                  <h4><?php echo esc_html__('Etkinlik Takvimi', 'artroplasti'); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html__('Etkinlik Takvimi', 'artroplasti'); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb end -->

<!-- Calendar Section Start -->
<div class="event-calendar-section ptb-100">
    <div class="container">
        <!-- Takvim Başlığı ve Yıl Seçici -->
        <div class="calendar-header">
            <div class="calendar-year-title">
                <h2><?php printf(esc_html__('%s Etkinlik Takvimi', 'artroplasti'), $current_year); ?></h2>
            </div>
            <div class="calendar-actions">
                <a href="<?php echo esc_url((function_exists('artroplasti_is_english_context') && artroplasti_is_english_context()) ? home_url('/en/events/') : get_post_type_archive_link('events')); ?>" class="btn btn-outline">
                    <?php _e('Liste Görünümü', 'artroplasti'); ?>
                </a>
                <div class="year-selector">
                    <select id="year-select" onchange="var currentMonth = new URLSearchParams(window.location.search).get('month') || '<?php echo $current_month; ?>'; window.location.search = '?month=' + currentMonth + '&year=' + this.value;">
                        <?php for ($y = 2025; $y <= 2030; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php selected($current_year, $y); ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Ay Tabları -->
        <div class="month-tabs">
            <?php foreach ($months_tr as $month_num => $month_name): ?>
                <a href="<?php echo esc_url(add_query_arg(array('month' => $month_num, 'year' => $current_year), $current_page_url)); ?>" 
                   class="month-tab <?php echo $month_num == $current_month ? 'active' : ''; ?>">
                    <?php echo $month_name; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Takvim Tablosu -->
        <div class="calendar-wrapper">
            <table class="event-calendar">
                <thead>
                    <tr>
                        <?php foreach ($days_tr as $day): ?>
                            <th><?php echo $day; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $day_count = 1;
                    $calendar_started = false;
                    
                    // Takvim satırları (maksimum 6 hafta)
                    for ($week = 0; $week < 6; $week++):
                        if ($day_count > $days_in_month) break;
                        ?>
                        <tr>
                            <?php for ($weekday = 1; $weekday <= 7; $weekday++): ?>
                                <td class="calendar-day <?php echo (($week == 0 && $weekday >= $first_day_weekday) || ($calendar_started && $day_count <= $days_in_month)) && isset($events_by_date[$day_count]) ? 'has-event' : ''; ?>">
                                    <?php
                                    if (($week == 0 && $weekday >= $first_day_weekday) || ($calendar_started && $day_count <= $days_in_month)) {
                                        $calendar_started = true;
                                        echo '<div class="day-number">' . $day_count . '</div>';
                                        
                                        // Etkinlikleri göster
                                        if (isset($events_by_date[$day_count]) && is_array($events_by_date[$day_count])) {
                                            foreach ($events_by_date[$day_count] as $event) {
                                                $is_start = (date('j', strtotime($event['start'])) == $day_count && 
                                                            date('n', strtotime($event['start'])) == $current_month);
                                                ?>
                                                <div class="event-item <?php echo $is_start ? 'event-start' : 'event-continue'; ?>">
                                                    <a href="<?php echo esc_url($event['url']); ?>">
                                                        <?php if ($is_start): ?>
                                                            <strong><?php echo esc_html($event['title']); ?></strong>
                                                            <?php if ($event['location']): ?>
                                                                <span class="event-location"><?php echo esc_html($event['location']); ?></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                        }
                                        $day_count++;
                                    } else {
                                        // Önceki/sonraki ayın günleri
                                        echo '<div class="day-number other-month"></div>';
                                    }
                                    ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- Yıllık Etkinlik Listesi -->
        <div class="calendar-events-list">
            <h3><?php printf(esc_html__('%s Yılı Tüm Etkinlikleri', 'artroplasti'), $current_year); ?></h3>
            <?php if ($year_events_query->have_posts()): ?>
            <div class="events-list-items">
                <?php
                // Etkinlikleri aya göre grupla
                $events_by_month = array();
                while ($year_events_query->have_posts()) : $year_events_query->the_post();
                    $event_start = get_post_meta(get_the_ID(), 'event_start_date', true);
                    $event_end = get_post_meta(get_the_ID(), 'event_end_date', true);
                    $event_location = get_post_meta(get_the_ID(), 'event_location', true);
                    
                    $month = intval(date('n', strtotime($event_start)));
                    
                    if (!isset($events_by_month[$month])) {
                        $events_by_month[$month] = array();
                    }
                    
                    $events_by_month[$month][] = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'start' => $event_start,
                        'end' => $event_end,
                        'location' => $event_location,
                        'url' => get_permalink()
                    );
                endwhile;
                wp_reset_postdata();
                
                // Her ay için etkinlikleri göster
                ksort($events_by_month);
                foreach ($events_by_month as $month => $month_events):
                    ?>
                    <div class="month-section">
                        <h4 class="month-section-title"><?php echo $months_tr[$month]; ?></h4>
                        <?php foreach ($month_events as $event):
                            $start_date = new DateTime($event['start']);
                            $end_date = new DateTime($event['end']);
                            ?>
                            <div class="event-list-item">
                                <div class="event-date-range">
                                    <span class="date-badge">
                                        <?php echo $start_date->format('d.m.Y'); ?> - <?php echo $end_date->format('d.m.Y'); ?>
                                    </span>
                                </div>
                                <div class="event-info">
                                    <h5><a href="<?php echo esc_url($event['url']); ?>"><?php echo esc_html($event['title']); ?></a></h5>
                                    <?php if ($event['location']): ?>
                                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo esc_html($event['location']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 40px; color: #999;">
                <?php echo sprintf(__('%s yılı için henüz etkinlik eklenmemiş.', 'artroplasti'), $current_year); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Calendar Section End -->

<?php get_footer(); ?>
