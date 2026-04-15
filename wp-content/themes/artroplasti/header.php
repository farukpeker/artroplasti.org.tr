<!DOCTYPE html>
<!--[if IE 8]>
<html <?php language_attributes(); ?> class="ie8 no-js">
<![endif]-->
<!--[if IE 9]>
<html <?php language_attributes(); ?> class="ie9 no-js">
<![endif]-->
<!--[if !IE]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <?php
    $theme_uri = get_template_directory_uri();
    $contact_email = get_theme_mod('artroplasti_contact_email', 'dernek@artroplasti.org.tr');
    $contact_phone = get_theme_mod('artroplasti_contact_phone', '+90 (000) 000 00 00');
    $contact_address = get_theme_mod('artroplasti_contact_address', 'Adres bilgisi');
    $contact_hours = get_theme_mod('artroplasti_contact_hours', "Hafta içi: 09:00 - 18:00\nHafta sonu: 10:00 - 16:00");
    $contact_page_url = get_theme_mod('artroplasti_contact_page_url', home_url('/iletisim'));

    $social_facebook  = get_theme_mod('artroplasti_social_facebook', 'https://www.facebook.com');
    $social_twitter   = get_theme_mod('artroplasti_social_twitter', 'https://www.twitter.com');
    $social_instagram = get_theme_mod('artroplasti_social_instagram', 'https://www.instagram.com');
    $social_linkedin  = get_theme_mod('artroplasti_social_linkedin', 'https://www.linkedin.com');
    $social_youtube   = get_theme_mod('artroplasti_social_youtube', '');

    $custom_logo_id  = get_theme_mod('custom_logo');
    $custom_logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';
    $custom_logo_alt = $custom_logo_id ? get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true) : '';
    if (empty($custom_logo_alt)) {
        $custom_logo_alt = get_bloginfo('name');
    }
    ?>

    <!-- favicon -->
    <?php wp_site_icon(); ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="preloader">
    <div id="status">
        <img src="<?php echo esc_url($theme_uri . '/assets/images/preloader.gif'); ?>" id="preloader_image" alt="loader">
    </div>
</div>

<!-- top to return -->
<a href="javascript:;" id="return-to-top"><i class="fas fa-angle-double-up"></i></a>

<!-- header start -->
<div class="main-header-wrapper float_left">
    <div class="sb-main-header">
        <div class="top-header-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="sb-top-left-section text-center text-md-start">
                            <div>
                                <a href="mailto:<?php echo esc_attr($contact_email); ?>"><span><i class="fas fa-envelope"></i></span>&nbsp;
                                    &nbsp;<?php echo esc_html($contact_email); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 d-none d-md-block">
                        <form class="d-flex justify-content-end ps-rel my-1 rounded" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <input class="" type="search" name="s" placeholder="Arama yap" value="<?php echo esc_attr(get_search_query()); ?>" aria-label="Search">
                            <button type="submit" class="border-0" aria-label="Search">
                                <span><i class="fas fa-search"></i></span>
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="sb-top-right-section">
                            <ul>
                                <li>
                                    <ul class="d-xl-flex d-lg-flex">
                                        <?php if (!empty($social_facebook)) : ?>
                                            <li><a href="<?php echo esc_url($social_facebook); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($social_twitter)) : ?>
                                            <li>
                                                <a href="<?php echo esc_url($social_twitter); ?>" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true" focusable="false" width="18" height="18">
                                                        <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($social_instagram)) : ?>
                                            <li><a href="<?php echo esc_url($social_instagram); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($social_linkedin)) : ?>
                                            <li><a href="<?php echo esc_url($social_linkedin); ?>" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($social_youtube)) : ?>
                                            <li><a href="<?php echo esc_url($social_youtube); ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <li class="login-btn">
                                    <span>
                                        <?php if (is_user_logged_in()) : ?>
                                            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Çıkış Yap</a>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url(home_url('/giris-yap')); ?>">Giriş Yap</a>
                                        <?php endif; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mid-header-section d-xl-block d-lg-block d-md-none d-sm-none d-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <div class="sb_logo_wrapper">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php if (!empty($custom_logo_url)) : ?>
                                    <img src="<?php echo esc_url($custom_logo_url); ?>" alt="<?php echo esc_attr($custom_logo_alt); ?>">
                                <?php else : ?>
                                    <img src="<?php echo esc_url($theme_uri . '/assets/images/logo.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="sb-mid-right-section">
                            <nav class="navbar navbar-expand-lg">
                                <?php
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'container'      => false,
                                    'menu_class'     => 'navbar-nav',
                                    'fallback_cb'    => false,
                                ));
                                ?>
                                <!-- Language Selector -->
                                <div class="language-selector">
                                    <button class="language-toggle" aria-label="Dil Seçimi">
                                        <i class="fas fa-globe"></i>
                                        <span class="language-code">
                                            <?php
                                            $locale = get_locale();
                                            if (strpos($locale, 'tr') !== false) {
                                                echo 'TR';
                                            } elseif (strpos($locale, 'en') !== false) {
                                                echo 'EN';
                                            } else {
                                                echo substr(strtoupper($locale), 0, 2);
                                            }
                                            ?>
                                        </span>
                                    </button>
                                    <div class="language-dropdown">
                                        <?php
                                        // WPML Support
                                        if (defined('ICL_LANGUAGE_CODE')) {
                                            do_action('wpml_add_language_selector');
                                        } 
                                        // Polylang Support
                                        elseif (function_exists('pll_the_languages')) {
                                            echo '<ul class="language-list">';
                                            pll_the_languages(array('show_flags' => 1, 'show_names' => 1));
                                            echo '</ul>';
                                        }
                                        // Fallback: Simple language switcher
                                        else {
                                            $languages = array(
                                                'tr' => array('name' => 'Türkçe', 'url' => home_url('/')),
                                                'en' => array('name' => 'English', 'url' => home_url('/en/')),
                                            );
                                            ?>
                                            <ul class="language-list">
                                                <?php foreach ($languages as $code => $lang) : 
                                                    $current_locale = strtolower(get_locale());
                                                    $is_active = (strpos($current_locale, $code) === 0);
                                                ?>
                                                    <li>
                                                        <a href="<?php echo esc_url($lang['url']); ?>" 
                                                           <?php echo $is_active ? 'class="active"' : ''; ?>>
                                                            <?php echo esc_html($lang['name']); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- mobile menu -->
        <div class="mobile-menu-wrapper d-xl-none d-lg-none d-md-block d-sm-block">
            <div class="container">
                <div class="row">
                    <div class=" col-md-6 col-sm-6 col-6">
                        <div class="mobile-logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php if (!empty($custom_logo_url)) : ?>
                                    <img src="<?php echo esc_url($custom_logo_url); ?>" alt="<?php echo esc_attr($custom_logo_alt); ?>">
                                <?php else : ?>
                                    <img src="<?php echo esc_url($theme_uri . '/assets/images/logo3.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="d-flex  justify-content-end">
                            <div class="d-flex align-items-center">
                                <div class="toggle-main-wrapper mt-2" id="sidebar-toggle">
                                    <span class="line"></span>
                                    <span class="line"></span>
                                    <span class="line"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="sidebar">
            <div class="sidebar_logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php if (!empty($custom_logo_url)) : ?>
                        <img src="<?php echo esc_url($custom_logo_url); ?>" alt="<?php echo esc_attr($custom_logo_alt); ?>">
                    <?php else : ?>
                        <img src="<?php echo esc_url($theme_uri . '/assets/images/logo.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <?php endif; ?>
                </a>
            </div>
            
            <div id="toggle_close">&times;</div>
            <div id='cssmenu'>
                <?php
                $mobile_menu_extra = '<li class="input-group border-none my-3 mx-2">'
                    . '<form role="search" method="get" action="' . esc_url(home_url('/')) . '">' 
                    . '<input type="search" name="s" class="form-control" placeholder="Arama yap" value="' . esc_attr(get_search_query()) . '" aria-label="Search">'
                    . '<button class="btn btn-outline-secondary" type="submit" id="button-addon2" aria-label="Search"><i class="fas fa-search"></i></button>'
                    . '</form>'
                    . '</li>'
                    . '<li class="border-none">'
                    . '<ul class="social-icon">'
                    . (!empty($social_facebook) ? '<li><a href="' . esc_url($social_facebook) . '" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>' : '')
                    . (!empty($social_twitter) ? '<li><a href="' . esc_url($social_twitter) . '" target="_blank" rel="noopener" aria-label="X (Twitter)"><svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 512 512" aria-hidden="true" focusable="false"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg></a></li>' : '')
                    . (!empty($social_instagram) ? '<li><a href="' . esc_url($social_instagram) . '" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>' : '')
                    . (!empty($social_linkedin) ? '<li><a href="' . esc_url($social_linkedin) . '" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a></li>' : '')
                    . (!empty($social_youtube) ? '<li><a href="' . esc_url($social_youtube) . '" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a></li>' : '')
                    . '</ul>'
                    . '</li>';

                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'float_left',
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s' . $mobile_menu_extra . '</ul>',
                    ));
                } else {
                    echo '<ul class="float_left">' . $mobile_menu_extra . '</ul>';
                }
                ?>
            </div>
                        <!-- Mobile Language Selector -->
            <div class="mobile-language-selector">
                <?php
                $current_locale = strtolower(get_locale());
                $is_tr_active = (strpos($current_locale, 'tr') === 0);
                $is_en_active = (strpos($current_locale, 'en') === 0);
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="lang-btn <?php echo $is_tr_active ? 'active' : ''; ?>">TR</a>
                <a href="<?php echo esc_url(home_url('/en/')); ?>" class="lang-btn <?php echo $is_en_active ? 'active' : ''; ?>">EN</a>
            </div>

        </div>
    </div>
</div>
<!-- header end -->
