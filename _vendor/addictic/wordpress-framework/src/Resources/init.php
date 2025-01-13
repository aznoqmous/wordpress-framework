<?php

namespace App;

use Addictic\WordpressFramework\Annotation\BlockManager;
use Addictic\WordpressFramework\Annotation\PostTypeManager;
use Addictic\WordpressFramework\Annotation\RouteManager;
use Addictic\WordpressFramework\Annotation\TaxonomyManager;
use Addictic\WordpressFramework\Models\CourseModel;
use Addictic\WordpressFramework\Models\FaqCategoryTaxonomyModel;
use Addictic\WordpressFramework\Models\FaqModel;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\JobOfferModel;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;
use Addictic\WordpressFramework\Models\Legacy\BlockModel;
use Addictic\WordpressFramework\Models\Legacy\PageModel;
use Addictic\WordpressFramework\Models\ModuleModel;
use Addictic\WordpressFramework\Models\QuizzModel;
use Addictic\WordpressFramework\Models\TestimonyModel;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

FormationModel::register();
CourseModel::register();
PageModel::register();
ThemeTaxonomyModel::register();
JobTaxonomyModel::register();
BlockModel::register();
AttachmentModel::register();
ModuleModel::register();
QuizzModel::register();
FaqModel::register();
FaqCategoryTaxonomyModel::register();
JobOfferModel::register();
TestimonyModel::register();

$routeManager = new RouteManager();
$routeManager->getRoutes();

$taxonomyManager = new TaxonomyManager();
$taxonomyManager->getTaxonomies();

$entityManager = new PostTypeManager();
$entityManager->getPostTypes();

$blocksManager = new BlockManager();
$blocksManager->getBlocks();