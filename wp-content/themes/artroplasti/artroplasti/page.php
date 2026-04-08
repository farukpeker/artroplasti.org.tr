<?php
/**
 * The template for displaying pages
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- breadcrumb start -->
<div class="contact-main-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="sb-contact-section">
                    <nav aria-label="breadcrumb">
                        <h4><?php the_title(); ?></h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                            <?php
                            // Display parent page if exists
                            if ($post->post_parent) {
                                $parent_id = $post->post_parent;
                                $parent_title = get_the_title($parent_id);
                                $parent_url = get_permalink($parent_id);
                                echo '<li class="breadcrumb-item"><a href="' . esc_url($parent_url) . '">' . esc_html($parent_title) . '</a></li>';
                            }
                            ?>
                            <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb end -->

<main id="main-content" class="site-main page">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <div class="section white py-5">
                            <div class="inner">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php the_content(); ?>
                                        </div> <!-- end .col-sm-12 -->
                                    </div> <!-- end .row -->
                                </div> <!-- end .container -->
                            </div> <!-- end .inner -->
                        </div>

           

            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
