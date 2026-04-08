<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>');
        endif;
        ?>
        <div class="entry-meta">
            <span class="posted-on"><?php echo get_the_date(); ?></span>
        </div>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="entry-thumbnail">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();
        else :
            the_excerpt();
        endif;
        ?>
    </div>

    <?php if (!is_singular()) : ?>
        <footer class="entry-footer">
            <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more">Devamını Oku</a>
        </footer>
    <?php endif; ?>
</article>
