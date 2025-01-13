<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\MenuHelper;
use Addictic\WordpressFramework\Helpers\QueryBuilder;
use WpOrg\Requests\Exception;

class FaqCategoryTaxonomyModel extends AbstractTaxonomyModel
{
    public static $strName = "faqCategory";
}