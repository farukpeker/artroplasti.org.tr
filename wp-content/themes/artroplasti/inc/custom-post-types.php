<?php
/**
 * Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

// Kurslar Post Type
function artroplasti_register_courses_post_type() {
    $labels = array(
        'name'               => 'Kurslar',
        'singular_name'      => 'Kurs',
        'menu_name'          => 'Kurslar',
        'add_new'            => 'Yeni Ekle',
        'add_new_item'       => 'Yeni Kurs Ekle',
        'edit_item'          => 'Kursu Düzenle',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'kurslar'),
        'show_in_rest'       => true,
    );

    register_post_type('courses', $args);
}
add_action('init', 'artroplasti_register_courses_post_type');

// Kongreler Post Type
function artroplasti_register_congresses_post_type() {
    $labels = array(
        'name'               => 'Kongreler',
        'singular_name'      => 'Kongre',
        'menu_name'          => 'Kongreler',
        'add_new'            => 'Yeni Ekle',
        'add_new_item'       => 'Yeni Kongre Ekle',
        'edit_item'          => 'Kongreyi Düzenle',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'kongreler'),
        'show_in_rest'       => true,
    );

    register_post_type('congresses', $args);
}
add_action('init', 'artroplasti_register_congresses_post_type');

// Webinarlar Post Type
function artroplasti_register_webinars_post_type() {
    $labels = array(
        'name'               => 'Webinarlar',
        'singular_name'      => 'Webinar',
        'menu_name'          => 'Webinarlar',
        'add_new'            => 'Yeni Ekle',
        'add_new_item'       => 'Yeni Webinar Ekle',
        'edit_item'          => 'Webinarı Düzenle',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-video-alt3',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'webinarlar'),
        'show_in_rest'       => true,
    );

    register_post_type('webinars', $args);
}
add_action('init', 'artroplasti_register_webinars_post_type');

// Banner Slider Post Type
function artroplasti_register_banner_post_type() {
    $labels = array(
        'name'               => 'Sliderlar',
        'singular_name'      => 'Slider',
        'menu_name'          => 'Sliderlar',
        'add_new'            => 'Yeni Ekle',
        'add_new_item'       => 'Yeni Slider Ekle',
        'edit_item'          => 'Slider Düzenle',
        'new_item'           => 'Yeni Slider',
        'view_item'          => 'Slider Görüntüle',
        'search_items'       => 'Slider Ara',
        'not_found'          => 'Slider bulunamadı',
        'not_found_in_trash' => 'Çöp kutusunda slider bulunamadı',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-images-alt2',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'rewrite'            => array('slug' => 'slider'),
        'show_in_rest'       => true,
    );

    register_post_type('banner_slide', $args);
}
add_action('init', 'artroplasti_register_banner_post_type');

// Sizin için Seçtiklerimiz Post Type
function artroplasti_register_featured_post_type() {
    $labels = array(
        'name'               => 'Sizin için Seçtiklerimiz',
        'singular_name'      => 'Öne Çıkan',
        'menu_name'          => 'Sizin için Seçtiklerimiz',
        'add_new'            => 'Yeni Ekle',
        'add_new_item'       => 'Yeni Öne Çıkan Ekle',
        'edit_item'          => 'Öne Çıkanı Düzenle',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-star-filled',
        'supports'           => array('title', 'thumbnail', 'page-attributes'),
        'rewrite'            => array('slug' => 'sectiklarimiz'),
        'show_in_rest'       => true,
    );

    register_post_type('featured_items', $args);
}
add_action('init', 'artroplasti_register_featured_post_type');
