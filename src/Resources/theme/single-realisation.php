<?php

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;

$realisation = \App\Models\RealisationModel::findActive();
$realisation->loadFields();

?>
<?= get_header(); ?>
<div class="single single-realisation">
    <div class="content row">
        <?php if ($images = $realisation->getImages()): ?>
            <div class="images">
                <?= Container::get("twig")->render("parts/slider.twig", ['items' => array_map(fn($item) => "<figure><img src=\"{$item->guid}\" alt=\"{$realisation->title}\"></figure>", $images->getModels())]) ?>
            </div>
        <?php endif; ?>
        <h1><?= get_the_title() ?></h1>
        <div class="informations">
            <h2>Infos clés</h2>
            <div>
                <span>Nombre de places de parking :</span>
                <strong><?= $realisation->parking_spaces ?></strong>
            </div>
            <div>
                <span>Superficie solaire :</span>
                <strong><?= $realisation->area ?>m²</strong>
            </div>
            <div>
                <span>Production annuelle :</span>
                <strong><?= $realisation->annual_production ?>MW</strong>
            </div>
            <div>
                <span>Puissance installée :</span>
                <strong><?= $realisation->power ?>MWh</strong>
            </div>
            <div>
                <span>Taux de couverture prévisionnel :</span>
                <strong><?= $realisation->coverage ?>%</strong>
            </div>
        </div>

        <div class="description">
            <h2>Description</h2>
            <p><?= $realisation->getValue("description") ?></p>
        </div>
        <?php if ($options = $realisation->getOptions()): ?>
            <div class="options">
                <?= Container::get("twig")->render("parts/slider.twig", ['items' => array_map(fn($item) => $item->renderListItem(), $options->getModels())]) ?>
            </div>
        <?php endif; ?>
        <div class="left wp-content">
            <h2>Détails</h2>
            <?= the_content(); ?>
        </div>
    </div>
</div>

