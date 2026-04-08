<div class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title">İçerik Bulunamadı</h1>
    </header>

    <div class="page-content">
        <?php if (is_search()) : ?>
            <p>Aradığınız kriterlere uygun içerik bulunamadı. Lütfen farklı anahtar kelimelerle tekrar deneyin.</p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p>Görünüşe göre burada hiçbir şey yok. Arama yapmayı deneyin?</p>
            <?php get_search_form(); ?>
        <?php endif; ?>
    </div>
</div>
