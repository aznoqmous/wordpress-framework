<?php

use Addictic\WordpressFramework\Helpers\Container;

$currentNews = \Addictic\WordpressFramework\Models\Legacy\PostModel::findActive();
$newsPage = \Addictic\WordpressFramework\Models\Legacy\PageModel::findBySourceLanguageId(get_option("newsPage"));
?>
<?= get_header(); ?>
<div class="single single-post">
    <div class="banner">
        <div class="metas">
            <div class="time">
                <?= Container::get("translator")->trans("post.published_at") ?>
                <time><?= get_the_date("d/m/Y") ?></time>
            </div>
            <div class="categories">
                <?php foreach (get_the_category() as $category): ?>
                    <div class="category"><?= $category->name ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($newsPage): ?>
            <a href="<?= $newsPage->getFrontendUrl() ?>">
                <i class="icon-caret-left"></i>
                <span><?= Container::get("translator")->trans("post.back") ?></span>
            </a>
        <?php endif; ?>
    </div>
    <div class="content row">
        <h1><?= get_the_title() ?></h1>
        <figure>
            <?= get_the_post_thumbnail() ?>
        </figure>
        <div class="left wp-content">
            <?= the_content(); ?>
            <a class="back" href="<?= $newsPage->getFrontendUrl() ?>">
                <i class="icon-caret-left"></i>
                <span><?= Container::get("translator")->trans("post.back") ?> </span>
            </a>
        </div>
    </div>
</div>
<?= get_footer(); ?>

