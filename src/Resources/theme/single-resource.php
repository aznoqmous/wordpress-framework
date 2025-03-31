<?php

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;

$resource = \App\Models\ResourceModel::findActive();
$parent = $resource->getParent();
?>
<?= get_header(); ?>
<div class="single single-testimony">
    <div class="content row">
        <ul class="breadcrumb">
            <li><a href="">Ressources</a></li>
            <?php if ($parent): ?>
                <li><a href="<?= $parent->guid ?>"><?= $parent->title ?></a></li>
            <?php endif; ?>
            <li><span><?= $parent ? "F.A.Q." : get_the_title() ?></span></li>
        </ul>
        <h1><?= $parent ? "F.A.Q." : get_the_title() ?></h1>
        <div class="left wp-content">
            <?php
            /**
             * FAQ
             */
            if ($parent): ?>
                <div class="filters">
                    <ul>
                        <li>Tout</li>
                        <?php foreach ($parent->getChildren() as $child): ?>
                        <li><?= $child->title ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php foreach ($parent->getChildren() as $child): ?>
                    <h2><?= $child->title ?></h2>
                    <?= $child->parseContent() ?>
                <?php endforeach; ?>
            <?php
            /**
             * SINGLE
             */
            else: ?>
            <?= the_content(); ?>
            <div class="wp-block-dvs-content-block has-grey-transparent-gradient-background has-background">
                <h2>F.A.Q.</h2>
                <p>
                    Dorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eu turpis molestie, dictum est a, mattis tellus. Sed dignissim, metus nec fringilla accumsan, risus sem sollicitudin lacus, ut interdum tellus elit sed risus. Maecenas eget condimentum velit, sit amet feugiat lectus.
                </p>
                <div class="wp-block-dvs-button wp-block-button-container">
                    <div class="wp-block-button">
                        <a href="">
                            <span>
                                En savoir plus
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= get_footer(); ?>
