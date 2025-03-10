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
            <a class="back" href="<?= $testimonyPage->getFrontendUrl() ?>">
                <i class="icon-caret-left"></i>
                <span><?= Container::get("translator")->trans("testimony.back") ?> </span>
            </a>
        </div>
    </div>
</div>
<?= get_footer(); ?>
