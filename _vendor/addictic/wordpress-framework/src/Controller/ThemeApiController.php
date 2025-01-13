<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

class ThemeApiController extends AbstractController
{

    /**
     * @Route("/theme/list", name="theme_list")
     */
    public function getThemes()
    {
        $themes = ThemeTaxonomyModel::findBy(["parent = 0"]);
        return $themes->fetchRows();
    }

    /**
     * @Route("/subtheme/list", name="theme_list")
     */
    public function getSubthemes()
    {
        $themes = ThemeTaxonomyModel::findBy(["parent != 0"]);
        return $themes->fetchRows();
    }
}