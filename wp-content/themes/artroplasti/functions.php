<?php
/**
 * Artroplasti Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Returns the default thumbnail URL used site-wide when a post has no featured image.
 */
function artroplasti_default_thumb(): string {
    return wp_upload_dir()['baseurl'] . '/2026/04/default.jpg';
}
// Theme setup
// Add query string support for calendar template
function artroplasti_query_vars( $query_vars ) {
    $query_vars[] = 'month';
    $query_vars[] = 'year';
    return $query_vars;
}
add_filter( 'query_vars', 'artroplasti_query_vars' );
// Fix 404 for calendar pages with query strings
function artroplasti_fix_calendar_404() {
    if ( is_page( 'etkinlik-takvimi' ) && ( isset( $_GET['month'] ) || isset( $_GET['year'] ) ) ) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header( 200 );
    }
}
add_action( 'template_redirect', 'artroplasti_fix_calendar_404' );
function artroplasti_setup() {
    // Make theme available for translation
    load_theme_textdomain('artroplasti', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Enable custom logo support
    add_theme_support('custom-logo', array(
        'height'      => 120,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Ana Menü', 'artroplasti'),
        'footer'  => __('Alt Menü', 'artroplasti'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');
}
add_action('after_setup_theme', 'artroplasti_setup');

// Enqueue scripts and styles
function artroplasti_scripts() {
    // Main stylesheet
    wp_enqueue_style('artroplasti-style', get_stylesheet_uri(), array(), '1.0.0');

    $theme_uri = get_template_directory_uri();

    // Template styles
    wp_enqueue_style('artroplasti-animate', $theme_uri . '/assets/css/animate.css', array(), '1.0.0');
    wp_enqueue_style('artroplasti-animate-min', $theme_uri . '/assets/css/animate.min.css', array('artroplasti-animate'), '1.0.0');
    wp_enqueue_style('artroplasti-bootstrap', $theme_uri . '/assets/css/bootstrap.min.css', array(), '5.0.0');
    wp_enqueue_style('artroplasti-fonts', $theme_uri . '/assets/css/fonts.css', array(), '1.0.0');
    wp_enqueue_style('artroplasti-fontawesome', $theme_uri . '/assets/css/font-awesome.css', array(), '1.0.0');
    wp_enqueue_style('artroplasti-fontawesome-min', $theme_uri . '/assets/css/font-awesome.min.css', array('artroplasti-fontawesome'), '1.0.0');
    // Font Awesome CDN as backup
    wp_enqueue_style('font-awesome-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1');
    wp_enqueue_style('artroplasti-magnific', $theme_uri . '/assets/css/magnific-popup.css', array(), '1.0.0');
    wp_enqueue_style('artroplasti-owl', $theme_uri . '/assets/css/owl.carousel.min.css', array(), '1.0.0');
    wp_enqueue_style('artroplasti-owl-theme', $theme_uri . '/assets/css/owl.theme.default.min.css', array('artroplasti-owl'), '1.0.0');
    wp_enqueue_style('artroplasti-template', $theme_uri . '/assets/css/style.css', array('artroplasti-bootstrap'), '1.0.0');
    wp_enqueue_style('artroplasti-responsive', $theme_uri . '/assets/css/responsive.css', array('artroplasti-template'), '1.0.0');

    // Custom CSS
    wp_enqueue_style('artroplasti-custom', $theme_uri . '/assets/css/custom.css', array('artroplasti-responsive'), '1.0.0');
    
    // Owl Carousel JS
    wp_enqueue_script('artroplasti-owl', $theme_uri . '/assets/js/owl.carousel.min.js', array('jquery'), '2.3.4', true);

    // Custom JavaScript
    wp_enqueue_script('artroplasti-custom', $theme_uri . '/assets/js/custom.js', array('jquery', 'artroplasti-owl'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('artroplasti-custom', 'artroplasti_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('artroplasti_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'artroplasti_scripts');

// Theme Customizer: Contact & Social Settings
function artroplasti_customize_register($wp_customize) {
    // Footer Logo Section
    $wp_customize->add_section('artroplasti_footer_logo_section', array(
        'title'    => __('Footer Logo', 'artroplasti'),
        'priority' => 29,
    ));

    $wp_customize->add_setting('artroplasti_footer_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'artroplasti_footer_logo', array(
        'label'     => __('Footer Logo', 'artroplasti'),
        'section'   => 'artroplasti_footer_logo_section',
        'mime_type' => 'image',
    )));

    $wp_customize->add_section('artroplasti_contact_section', array(
        'title'    => __('İletişim Bilgileri', 'artroplasti'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('artroplasti_contact_email', array(
        'default'           => 'dernek@artroplasti.org.tr',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('artroplasti_contact_email', array(
        'label'   => __('E-posta', 'artroplasti'),
        'section' => 'artroplasti_contact_section',
        'type'    => 'email',
    ));

    $wp_customize->add_setting('artroplasti_contact_phone', array(
        'default'           => '+90 (000) 000 00 00',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('artroplasti_contact_phone', array(
        'label'   => __('Telefon', 'artroplasti'),
        'section' => 'artroplasti_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('artroplasti_contact_address', array(
        'default'           => 'Adres bilgisi',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('artroplasti_contact_address', array(
        'label'   => __('Adres', 'artroplasti'),
        'section' => 'artroplasti_contact_section',
        'type'    => 'textarea',
    ));

    $wp_customize->add_control('artroplasti_contact_skype', array(
        'label'   => __('Skype', 'artroplasti'),
        'section' => 'artroplasti_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('artroplasti_contact_hours', array(
        'default'           => 'Hafta içi: 09:00 - 18:00\nHafta sonu: 10:00 - 16:00',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('artroplasti_contact_hours', array(
        'label'   => __('Çalışma Saatleri', 'artroplasti'),
        'section' => 'artroplasti_contact_section',
        'type'    => 'textarea',
    ));

    $wp_customize->add_setting('artroplasti_contact_page_url', array(
        'default'           => home_url('/iletisim'),
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('artroplasti_contact_page_url', array(
        'label'   => __('İletişim Sayfası URL', 'artroplasti'),
        'section' => 'artroplasti_contact_section',
        'type'    => 'url',
    ));

    $wp_customize->add_section('artroplasti_social_section', array(
        'title'    => __('Sosyal Medya', 'artroplasti'),
        'priority' => 31,
    ));

    $social_settings = array(
        'artroplasti_social_facebook'  => array('label' => 'Facebook', 'default' => 'https://www.facebook.com'),
        'artroplasti_social_twitter'   => array('label' => 'X (Twitter)', 'default' => 'https://www.twitter.com'),
        'artroplasti_social_instagram' => array('label' => 'Instagram', 'default' => 'https://www.instagram.com'),
        'artroplasti_social_linkedin'  => array('label' => 'LinkedIn', 'default' => 'https://www.linkedin.com'),
        'artroplasti_social_youtube'   => array('label' => 'YouTube', 'default' => 'https://www.youtube.com'),
    );

    foreach ($social_settings as $setting_id => $setting_data) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => $setting_data['default'],
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'   => $setting_data['label'],
            'section' => 'artroplasti_social_section',
            'type'    => 'url',
        ));
    }
}
add_action('customize_register', 'artroplasti_customize_register');

// Helper: safely format line breaks
function artroplasti_nl2br($text) {
    return nl2br(esc_html($text));
}

// Include custom post types
require_once get_template_directory() . '/inc/custom-post-types.php';

// Include user functions
require_once get_template_directory() . '/inc/user-functions.php';

// Include payment functions
require_once get_template_directory() . '/inc/payment-functions.php';

// Include member management
require_once get_template_directory() . '/inc/members.php';

// Include dues management
require_once get_template_directory() . '/inc/dues.php';

// Include İş Bankası POS integration
require_once get_template_directory() . '/inc/payment-isbank.php';

// Widget areas
function artroplasti_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'artroplasti'),
        'id'            => 'sidebar-1',
        'description'   => __('Ana sidebar widget alanı', 'artroplasti'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 1', 'artroplasti'),
        'id'            => 'footer-1',
        'description'   => __('Footer birinci alan', 'artroplasti'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 2', 'artroplasti'),
        'id'            => 'footer-2',
        'description'   => __('Footer ikinci alan', 'artroplasti'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 3', 'artroplasti'),
        'id'            => 'footer-3',
        'description'   => __('Footer üçüncü alan', 'artroplasti'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'artroplasti_widgets_init');

// Banner Slider Meta Box
function artroplasti_add_banner_slider_metabox() {
    add_meta_box(
        'artroplasti_banner_slider_meta',
        __('Slider Buton Ayarları', 'artroplasti'),
        'artroplasti_render_banner_slider_metabox',
        'banner_slide',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_banner_slider_metabox');

function artroplasti_render_banner_slider_metabox($post) {
    wp_nonce_field('artroplasti_banner_slider_meta_nonce', 'artroplasti_banner_slider_meta_nonce');
    $button_text = get_post_meta($post->ID, 'banner_button_text', true);
    $button_url = get_post_meta($post->ID, 'banner_button_url', true);
    ?>
    <p>
        <label for="banner_button_text"><strong><?php echo esc_html__('Buton Metni', 'artroplasti'); ?></strong></label>
        <input type="text" id="banner_button_text" name="banner_button_text" value="<?php echo esc_attr($button_text); ?>" class="widefat" placeholder="<?php echo esc_attr__('Devamı', 'artroplasti'); ?>">
    </p>
    <p>
        <label for="banner_button_url"><strong><?php echo esc_html__('Buton URL', 'artroplasti'); ?></strong></label>
        <input type="url" id="banner_button_url" name="banner_button_url" value="<?php echo esc_attr($button_url); ?>" class="widefat" placeholder="https://">
    </p>
    <?php
}

function artroplasti_save_banner_slider_meta($post_id) {
    if (!isset($_POST['artroplasti_banner_slider_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['artroplasti_banner_slider_meta_nonce'], 'artroplasti_banner_slider_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'banner_slide') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['banner_button_text'])) {
        update_post_meta($post_id, 'banner_button_text', sanitize_text_field($_POST['banner_button_text']));
    }

    if (isset($_POST['banner_button_url'])) {
        update_post_meta($post_id, 'banner_button_url', esc_url_raw($_POST['banner_button_url']));
    }
}
add_action('save_post_banner_slide', 'artroplasti_save_banner_slider_meta');

// Blog Manual Date Meta Box
function artroplasti_add_blog_date_metabox() {
    add_meta_box(
        'artroplasti_blog_date_meta',
        __('Manuel Tarih (İsteğe Bağlı)', 'artroplasti'),
        'artroplasti_render_blog_date_metabox',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_blog_date_metabox');

function artroplasti_render_blog_date_metabox($post) {
    wp_nonce_field('artroplasti_blog_date_meta_nonce', 'artroplasti_blog_date_meta_nonce');
    $manual_date = get_post_meta($post->ID, 'blog_manual_date', true);
    ?>
    <p>
        <label for="blog_manual_date"><strong><?php echo esc_html__('Manuel Tarih (Örn: 13-14 Şubat 2026)', 'artroplasti'); ?></strong></label>
        <input type="text" id="blog_manual_date" name="blog_manual_date" value="<?php echo esc_attr($manual_date); ?>" class="widefat" placeholder="<?php echo esc_attr__('13-14 Şubat 2026', 'artroplasti'); ?>">
        <small><?php echo esc_html__('Boş bırakılırsa yazının yayınlanma tarihi kullanılır.', 'artroplasti'); ?></small>
    </p>
    <?php
}

function artroplasti_save_blog_date_meta($post_id) {
    if (!isset($_POST['artroplasti_blog_date_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['artroplasti_blog_date_meta_nonce'], 'artroplasti_blog_date_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'post') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['blog_manual_date'])) {
        update_post_meta($post_id, 'blog_manual_date', sanitize_text_field($_POST['blog_manual_date']));
    }
}
add_action('save_post', 'artroplasti_save_blog_date_meta');

// Blog PDF Upload Meta Box
function artroplasti_add_blog_pdf_metabox() {
    add_meta_box(
        'artroplasti_blog_pdf_meta',
        __('PDF Dosyası (İsteğe Bağlı)', 'artroplasti'),
        'artroplasti_render_blog_pdf_metabox',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_blog_pdf_metabox');

function artroplasti_render_blog_pdf_metabox($post) {
    wp_nonce_field('artroplasti_blog_pdf_meta_nonce', 'artroplasti_blog_pdf_meta_nonce');
    $pdf_url = get_post_meta($post->ID, 'blog_pdf_url', true);
    ?>
    <p>
        <label for="blog_pdf_url"><strong><?php echo esc_html__('PDF URL', 'artroplasti'); ?></strong></label>
        <input type="text" id="blog_pdf_url" name="blog_pdf_url" value="<?php echo esc_attr($pdf_url); ?>" class="widefat" placeholder="https://..." style="margin-bottom: 6px;">
        <button type="button" class="button" id="artroplasti_pdf_upload_btn"><?php echo esc_html__('PDF Seç / Yükle', 'artroplasti'); ?></button>
        <?php if (!empty($pdf_url)) : ?>
            <span style="margin-left:10px;"><a href="<?php echo esc_url($pdf_url); ?>" target="_blank"><?php echo esc_html__('Mevcut PDF\'yi Görüntüle', 'artroplasti'); ?></a></span>
        <?php endif; ?>
        <br><small><?php echo esc_html__('Boş bırakılırsa PDF butonu görünmez.', 'artroplasti'); ?></small>
    </p>
    <script>
    jQuery(function($) {
        var mediaFrame;
        $('#artroplasti_pdf_upload_btn').on('click', function(e) {
            e.preventDefault();
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }
            mediaFrame = wp.media({
                title: '<?php echo esc_js(__('PDF Seç', 'artroplasti')); ?>',
                button: { text: '<?php echo esc_js(__('Seç', 'artroplasti')); ?>' },
                library: { type: 'application/pdf' },
                multiple: false
            });
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                $('#blog_pdf_url').val(attachment.url);
            });
            mediaFrame.open();
        });
    });
    </script>
    <?php
}

function artroplasti_save_blog_pdf_meta($post_id) {
    if (!isset($_POST['artroplasti_blog_pdf_meta_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['artroplasti_blog_pdf_meta_nonce'], 'artroplasti_blog_pdf_meta_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['post_type']) && $_POST['post_type'] === 'post') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }
    if (isset($_POST['blog_pdf_url'])) {
        $pdf_url = esc_url_raw(trim($_POST['blog_pdf_url']));
        update_post_meta($post_id, 'blog_pdf_url', $pdf_url);
    }
}
add_action('save_post', 'artroplasti_save_blog_pdf_meta');

// Enqueue WP media library script on post edit screens
function artroplasti_enqueue_admin_media($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'artroplasti_enqueue_admin_media');

// Congress Meta Box
function artroplasti_add_congress_metabox() {
    add_meta_box(
        'artroplasti_congress_meta',
        __('Kongre Bilgileri', 'artroplasti'),
        'artroplasti_render_congress_metabox',
        'congresses',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_congress_metabox');

function artroplasti_render_congress_metabox($post) {
    wp_nonce_field('artroplasti_congress_meta_nonce', 'artroplasti_congress_meta_nonce');
    $congress_date = get_post_meta($post->ID, 'congress_date', true);
    $congress_location = get_post_meta($post->ID, 'congress_location', true);
    $congress_site_url = get_post_meta($post->ID, 'congress_site_url', true);
    $congress_program_url = get_post_meta($post->ID, 'congress_program_url', true);
    $congress_site_label = get_post_meta($post->ID, 'congress_site_label', true);
    $congress_program_label = get_post_meta($post->ID, 'congress_program_label', true);
    $congress_website_url = get_post_meta($post->ID, 'congress_website_url', true);
    $congress_website_label = get_post_meta($post->ID, 'congress_website_label', true);
    ?>
    <p>
        <label for="congress_date"><strong><?php echo esc_html__('Tarih', 'artroplasti'); ?></strong></label>
        <input type="text" id="congress_date" name="congress_date" value="<?php echo esc_attr($congress_date); ?>" class="widefat" placeholder="<?php echo esc_attr__('Mart-Nisan 2027', 'artroplasti'); ?>">
    </p>
    <p>
        <label for="congress_location"><strong><?php echo esc_html__('Yer', 'artroplasti'); ?></strong></label>
        <input type="text" id="congress_location" name="congress_location" value="<?php echo esc_attr($congress_location); ?>" class="widefat" placeholder="<?php echo esc_attr__('Antalya, Türkiye', 'artroplasti'); ?>">
    </p>
    <p>
        <label for="congress_site_url"><strong><?php echo esc_html__('Toplantı Web Sitesi URL', 'artroplasti'); ?></strong></label>
        <input type="url" id="congress_site_url" name="congress_site_url" value="<?php echo esc_attr($congress_site_url); ?>" class="widefat" placeholder="https://">
    </p>
    <p>
        <label for="congress_site_label"><strong><?php echo esc_html__('Toplantı Web Sitesi Buton Metni', 'artroplasti'); ?></strong></label>
        <input type="text" id="congress_site_label" name="congress_site_label" value="<?php echo esc_attr($congress_site_label); ?>" class="widefat" placeholder="<?php echo esc_attr__('Toplantı Web Sitesi (Yakında)', 'artroplasti'); ?>">
    </p>
    <p>
        <label for="congress_program_url"><strong><?php echo esc_html__('Kongre Programı URL', 'artroplasti'); ?></strong></label>
        <input type="url" id="congress_program_url" name="congress_program_url" value="<?php echo esc_attr($congress_program_url); ?>" class="widefat" placeholder="https://">
    </p>
    <p>
        <label for="congress_program_label"><strong><?php echo esc_html__('Kongre Programı Buton Metni', 'artroplasti'); ?></strong></label>
        <input type="text" id="congress_program_label" name="congress_program_label" value="<?php echo esc_attr($congress_program_label); ?>" class="widefat" placeholder="<?php echo esc_attr__('Kongre Programı (Yakında)', 'artroplasti'); ?>">
    </p>
    <p>
        <label for="congress_website_url"><strong><?php echo esc_html__('Web Sitesi URL', 'artroplasti'); ?></strong></label>
        <input type="url" id="congress_website_url" name="congress_website_url" value="<?php echo esc_attr($congress_website_url); ?>" class="widefat" placeholder="https://">
    </p>
    <p>
        <label for="congress_website_label"><strong><?php echo esc_html__('Web Sitesi Buton Metni', 'artroplasti'); ?></strong></label>
        <input type="text" id="congress_website_label" name="congress_website_label" value="<?php echo esc_attr($congress_website_label); ?>" class="widefat" placeholder="<?php echo esc_attr__('Web sitesi', 'artroplasti'); ?>">
    </p>
    <?php
}

function artroplasti_save_congress_meta($post_id) {
    if (!isset($_POST['artroplasti_congress_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['artroplasti_congress_meta_nonce'], 'artroplasti_congress_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'congresses') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['congress_date'])) {
        update_post_meta($post_id, 'congress_date', sanitize_text_field($_POST['congress_date']));
    }

    if (isset($_POST['congress_location'])) {
        update_post_meta($post_id, 'congress_location', sanitize_text_field($_POST['congress_location']));
    }

    if (isset($_POST['congress_site_url'])) {
        update_post_meta($post_id, 'congress_site_url', esc_url_raw($_POST['congress_site_url']));
    }

    if (isset($_POST['congress_program_url'])) {
        update_post_meta($post_id, 'congress_program_url', esc_url_raw($_POST['congress_program_url']));
    }

    if (isset($_POST['congress_site_label'])) {
        update_post_meta($post_id, 'congress_site_label', sanitize_text_field($_POST['congress_site_label']));
    }

    if (isset($_POST['congress_program_label'])) {
        update_post_meta($post_id, 'congress_program_label', sanitize_text_field($_POST['congress_program_label']));
    }

    if (isset($_POST['congress_website_url'])) {
        update_post_meta($post_id, 'congress_website_url', esc_url_raw($_POST['congress_website_url']));
    }

    if (isset($_POST['congress_website_label'])) {
        update_post_meta($post_id, 'congress_website_label', sanitize_text_field($_POST['congress_website_label']));
    }
}
add_action('save_post_congresses', 'artroplasti_save_congress_meta');

// Courses External Link Meta Box
function artroplasti_add_course_link_metabox() {
    add_meta_box(
        'artroplasti_course_link_meta',
        __('Kurs Dış Link', 'artroplasti'),
        'artroplasti_render_course_link_metabox',
        'courses',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_course_link_metabox');

function artroplasti_render_course_link_metabox($post) {
    wp_nonce_field('artroplasti_course_link_meta_nonce', 'artroplasti_course_link_meta_nonce');
    $course_external_url = get_post_meta($post->ID, 'course_external_url', true);
    ?>
    <p>
        <label for="course_external_url"><strong><?php echo esc_html__('Dış Link (İncele Butonu)', 'artroplasti'); ?></strong></label>
        <input type="url" id="course_external_url" name="course_external_url" value="<?php echo esc_attr($course_external_url); ?>" class="widefat" placeholder="https://">
        <small><?php echo esc_html__('Boş bırakılırsa kurs detay sayfası açılır.', 'artroplasti'); ?></small>
    </p>
    <?php
}

function artroplasti_save_course_link_meta($post_id) {
    if (!isset($_POST['artroplasti_course_link_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['artroplasti_course_link_meta_nonce'], 'artroplasti_course_link_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'courses') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['course_external_url'])) {
        update_post_meta($post_id, 'course_external_url', esc_url_raw($_POST['course_external_url']));
    }
}
add_action('save_post_courses', 'artroplasti_save_course_link_meta');

// Featured Items Link Meta Box
function artroplasti_add_featured_link_metabox() {
    add_meta_box(
        'artroplasti_featured_link_meta',
        __('Link Ayarları', 'artroplasti'),
        'artroplasti_render_featured_link_metabox',
        'featured_items',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_featured_link_metabox');

function artroplasti_render_featured_link_metabox($post) {
    wp_nonce_field('artroplasti_featured_link_meta_nonce', 'artroplasti_featured_link_meta_nonce');
    $link_type = get_post_meta($post->ID, 'featured_link_type', true);
    $internal_page = get_post_meta($post->ID, 'featured_internal_page', true);
    $external_url = get_post_meta($post->ID, 'featured_external_url', true);
    
    if (empty($link_type)) {
        $link_type = 'internal';
    }
    ?>
    <p>
        <label><strong><?php echo esc_html__('Link Tipi:', 'artroplasti'); ?></strong></label><br>
        <label>
            <input type="radio" name="featured_link_type" value="internal" <?php checked($link_type, 'internal'); ?>>
            <?php echo esc_html__('İç Sayfa (WordPress Sayfası)', 'artroplasti'); ?>
        </label><br>
        <label>
            <input type="radio" name="featured_link_type" value="external" <?php checked($link_type, 'external'); ?>>
            <?php echo esc_html__('Dış Bağlantı (Harici URL)', 'artroplasti'); ?>
        </label>
    </p>
    
    <p class="featured-internal-field" style="<?php echo ($link_type === 'external') ? 'display:none;' : ''; ?>">
        <label for="featured_internal_page"><strong><?php echo esc_html__('Sayfa Seç:', 'artroplasti'); ?></strong></label>
        <?php
        wp_dropdown_pages(array(
            'name'              => 'featured_internal_page',
            'id'                => 'featured_internal_page',
            'selected'          => $internal_page,
            'show_option_none'  => __('-- Sayfa Seçin --', 'artroplasti'),
            'option_none_value' => '',
        ));
        ?>
    </p>
    
    <p class="featured-external-field" style="<?php echo ($link_type === 'internal') ? 'display:none;' : ''; ?>">
        <label for="featured_external_url"><strong><?php echo esc_html__('Dış Bağlantı:', 'artroplasti'); ?></strong></label>
        <input type="url" id="featured_external_url" name="featured_external_url" value="<?php echo esc_attr($external_url); ?>" class="widefat" placeholder="https://">
    </p>
    
    <script>
    jQuery(document).ready(function($) {
        $('input[name="featured_link_type"]').on('change', function() {
            if ($(this).val() === 'internal') {
                $('.featured-internal-field').show();
                $('.featured-external-field').hide();
            } else {
                $('.featured-internal-field').hide();
                $('.featured-external-field').show();
            }
        });
    });
    </script>
    <?php
}

function artroplasti_save_featured_link_meta($post_id) {
    if (!isset($_POST['artroplasti_featured_link_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['artroplasti_featured_link_meta_nonce'], 'artroplasti_featured_link_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'featured_items') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['featured_link_type'])) {
        update_post_meta($post_id, 'featured_link_type', sanitize_text_field($_POST['featured_link_type']));
    }
    
    if (isset($_POST['featured_internal_page'])) {
        update_post_meta($post_id, 'featured_internal_page', intval($_POST['featured_internal_page']));
    }
    
    if (isset($_POST['featured_external_url'])) {
        update_post_meta($post_id, 'featured_external_url', esc_url_raw($_POST['featured_external_url']));
    }
}
add_action('save_post_featured_items', 'artroplasti_save_featured_link_meta');

// Webinar Meta Box
function artroplasti_add_webinar_metabox() {
    add_meta_box(
        'artroplasti_webinar_meta',
        esc_html__('Webinar Bilgileri', 'artroplasti'),
        'artroplasti_render_webinar_metabox',
        'webinars',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_webinars', 'artroplasti_add_webinar_metabox');

function artroplasti_render_webinar_metabox($post) {
    wp_nonce_field('artroplasti_webinar_meta_nonce', 'artroplasti_webinar_meta_nonce');
    $webinar_year = get_post_meta($post->ID, 'webinar_year', true);
    
    $current_year = date('Y');
    $years = array();
    for ($i = 2015; $i <= $current_year; $i++) {
        $years[$i] = $i;
    }
    krsort($years);
    ?>
    <p>
        <label for="webinar_year"><strong><?php echo esc_html__('Yıl', 'artroplasti'); ?></strong></label>
        <select id="webinar_year" name="webinar_year" class="widefat" required>
            <option value=""><?php echo esc_html__('Yıl Seçiniz', 'artroplasti'); ?></option>
            <?php foreach ($years as $year) : ?>
                <option value="<?php echo esc_attr($year); ?>" <?php selected($webinar_year, $year); ?>>
                    <?php echo esc_html($year); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

function artroplasti_save_webinar_meta($post_id) {
    if (!isset($_POST['artroplasti_webinar_meta_nonce']) || !wp_verify_nonce($_POST['artroplasti_webinar_meta_nonce'], 'artroplasti_webinar_meta_nonce')) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'webinars') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['webinar_year'])) {
        update_post_meta($post_id, 'webinar_year', intval($_POST['webinar_year']));
    }
}
add_action('save_post_webinars', 'artroplasti_save_webinar_meta');

// Register Events Custom Post Type
function artroplasti_register_events_post_type() {
    $labels = array(
        'name'                  => _x('Etkinlikler', 'Post Type General Name', 'artroplasti'),
        'singular_name'         => _x('Etkinlik', 'Post Type Singular Name', 'artroplasti'),
        'menu_name'             => __('Etkinlikler', 'artroplasti'),
        'name_admin_bar'        => __('Etkinlik', 'artroplasti'),
        'archives'              => __('Etkinlik Arşivi', 'artroplasti'),
        'attributes'            => __('Etkinlik Özellikleri', 'artroplasti'),
        'parent_item_colon'     => __('Üst Etkinlik:', 'artroplasti'),
        'all_items'             => __('Tüm Etkinlikler', 'artroplasti'),
        'add_new_item'          => __('Yeni Etkinlik Ekle', 'artroplasti'),
        'add_new'               => __('Yeni Ekle', 'artroplasti'),
        'new_item'              => __('Yeni Etkinlik', 'artroplasti'),
        'edit_item'             => __('Etkinliği Düzenle', 'artroplasti'),
        'update_item'           => __('Etkinliği Güncelle', 'artroplasti'),
        'view_item'             => __('Etkinliği Görüntüle', 'artroplasti'),
        'view_items'            => __('Etkinlikleri Görüntüle', 'artroplasti'),
        'search_items'          => __('Etkinlik Ara', 'artroplasti'),
        'not_found'             => __('Etkinlik bulunamadı', 'artroplasti'),
        'not_found_in_trash'    => __('Çöp kutusunda etkinlik bulunamadı', 'artroplasti'),
    );

    $args = array(
        'label'                 => __('Etkinlik', 'artroplasti'),
        'description'           => __('Etkinlik Takvimi', 'artroplasti'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-calendar-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'etkinlikler'),
    );

    register_post_type('events', $args);
}
add_action('init', 'artroplasti_register_events_post_type', 0);

// Add Events Meta Boxes
function artroplasti_add_events_meta_boxes() {
    add_meta_box(
        'artroplasti_event_details',
        __('Etkinlik Detayları', 'artroplasti'),
        'artroplasti_render_event_details_meta_box',
        'events',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'artroplasti_add_events_meta_boxes');

// Render Events Meta Box
function artroplasti_render_event_details_meta_box($post) {
    wp_nonce_field('artroplasti_event_meta_nonce', 'artroplasti_event_meta_nonce');
    
    $start_date = get_post_meta($post->ID, 'event_start_date', true);
    $end_date = get_post_meta($post->ID, 'event_end_date', true);
    $location = get_post_meta($post->ID, 'event_location', true);
    $event_type = get_post_meta($post->ID, 'event_type', true);
    ?>
    <p>
        <label for="event_start_date"><strong><?php _e('Başlangıç Tarihi:', 'artroplasti'); ?></strong></label><br>
        <input type="date" id="event_start_date" name="event_start_date" value="<?php echo esc_attr($start_date); ?>" style="width: 100%; max-width: 300px;" required>
    </p>
    <p>
        <label for="event_end_date"><strong><?php _e('Bitiş Tarihi:', 'artroplasti'); ?></strong></label><br>
        <input type="date" id="event_end_date" name="event_end_date" value="<?php echo esc_attr($end_date); ?>" style="width: 100%; max-width: 300px;" required>
    </p>
    <p>
        <label for="event_location"><strong><?php _e('Konum:', 'artroplasti'); ?></strong></label><br>
        <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($location); ?>" style="width: 100%;" placeholder="Örn: İstanbul, Ankara">
    </p>
    <p>
        <label for="event_type"><strong><?php _e('Etkinlik Türü:', 'artroplasti'); ?></strong></label><br>
        <select id="event_type" name="event_type" style="width: 100%; max-width: 300px;">
            <option value=""><?php _e('Seçiniz', 'artroplasti'); ?></option>
            <option value="kurs" <?php selected($event_type, 'kurs'); ?>><?php _e('Kurs', 'artroplasti'); ?></option>
            <option value="kongre" <?php selected($event_type, 'kongre'); ?>><?php _e('Kongre', 'artroplasti'); ?></option>
            <option value="toplanti" <?php selected($event_type, 'toplanti'); ?>><?php _e('Toplantı', 'artroplasti'); ?></option>
            <option value="sempozyum" <?php selected($event_type, 'sempozyum'); ?>><?php _e('Sempozyum', 'artroplasti'); ?></option>
        </select>
    </p>
    <?php
    $event_pdf_url = get_post_meta($post->ID, 'event_pdf_url', true);
    ?>
    <p>
        <label for="event_pdf_url"><strong><?php _e('PDF Dosyası (İsteğe Bağlı):', 'artroplasti'); ?></strong></label><br>
        <input type="url" id="event_pdf_url" name="event_pdf_url" value="<?php echo esc_attr($event_pdf_url); ?>" style="width: 100%;" placeholder="https://">
        <button type="button" class="button" id="event_pdf_btn" style="margin-top: 6px;"><?php _e('PDF Seç / Yükle', 'artroplasti'); ?></button>
    </p>
    <script>
    jQuery(function($) {
        $('#event_pdf_btn').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'PDF Seç', button: { text: 'Seç' }, multiple: false });
            frame.on('select', function() {
                var att = frame.state().get('selection').first().toJSON();
                $('#event_pdf_url').val(att.url);
            });
            frame.open();
        });
    });
    </script>
    <?php
}

// Save Events Meta Data
function artroplasti_save_event_meta($post_id) {
    if (!isset($_POST['artroplasti_event_meta_nonce']) || !wp_verify_nonce($_POST['artroplasti_event_meta_nonce'], 'artroplasti_event_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['event_start_date'])) {
        update_post_meta($post_id, 'event_start_date', sanitize_text_field($_POST['event_start_date']));
    }

    if (isset($_POST['event_end_date'])) {
        update_post_meta($post_id, 'event_end_date', sanitize_text_field($_POST['event_end_date']));
    }

    if (isset($_POST['event_location'])) {
        update_post_meta($post_id, 'event_location', sanitize_text_field($_POST['event_location']));
    }

    if (isset($_POST['event_type'])) {
        update_post_meta($post_id, 'event_type', sanitize_text_field($_POST['event_type']));
    }

    if (isset($_POST['event_pdf_url'])) {
        update_post_meta($post_id, 'event_pdf_url', esc_url_raw($_POST['event_pdf_url']));
    }
}
add_action('save_post_events', 'artroplasti_save_event_meta');
