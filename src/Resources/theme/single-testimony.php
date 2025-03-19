<?php

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;

$testimony = \App\Models\TestimonyModel::findActive();
$testimonyPage = \Addictic\WordpressFramework\Models\Legacy\PageModel::findById(get_option("testimonyPage"));

?>
<?= get_header(); ?>
<div class="single single-testimony">
    <div class="content row">
        <figure>
            <img src="<?= $testimony->getLogoPath() ?>" alt="">
        </figure>
        <h1><?= get_the_title() ?></h1>
        <div class="time">
            <?= Container::get("translator")->trans("post.published_at") ?>&nbsp;<time><?= get_the_date("d/m/Y") ?></time>
        </div>
        <div class="left wp-content">
            <?= the_content(); ?>

            <?php if($realisations = $testimony->getRealisations()): ?>
                <?= Container::get("twig")->render("parts/slider.twig", [
                        'title' => "Réalisations associées",
                        'items' => $testimony->getRealisations()->map(fn($el)=> $el->renderItem())
                ]) ?>
            <?php endif; ?>

            <a class="back" href="<?= $testimonyPage->getFrontendUrl() ?>">
                <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1.58105" y="1.42236" width="31" height="31" rx="15.5" stroke="#06345D" stroke-width="2"/>
                    <path d="M17.0808 23.7155L10.2877 16.9225M10.2877 16.9225L17.0809 10.1293M10.2877 16.9225L23.8739 16.9225" stroke="#06345D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span><?= Container::get("translator")->trans("testimony.back") ?> </span>
            </a>
        </div>
    </div>
</div>

<?= get_footer(); ?>
