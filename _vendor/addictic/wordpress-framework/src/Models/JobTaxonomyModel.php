<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Config;
use Addictic\WordpressFramework\Helpers\MenuHelper;
use Addictic\WordpressFramework\Helpers\WpmlHelper;

class JobTaxonomyModel extends AbstractTaxonomyModel
{
    public static $strName = "job";

    public function getFrontendUrl()
    {
        $formationPage = MenuHelper::getFormationsPage();
        return $formationPage->getFrontendUrl() . "?job=" . $this->slug;
    }

    public static function findJobs()
    {
        $jobIds = Config::getJson("jobs") ?: [];
        if(!WpmlHelper::isDefaultLanguage()) $jobs = self::findBySourceLanguageIds($jobIds);
        else $jobs = self::findByIds($jobIds);
        return $jobs;
    }
}