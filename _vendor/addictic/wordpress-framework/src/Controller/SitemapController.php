<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\RoutingHelper;
use Addictic\WordpressFramework\Helpers\WpmlHelper;
use Addictic\WordpressFramework\Models\CourseModel;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\Legacy\PageModel;

class SitemapController extends AbstractController
{

    /**
     * @Route("/sitemap.xml", name="sitemap")
     */
    public function getThemes()
    {
        $pages = [
            ...PageModel::findAll([], PageModel::getAllLanguageBaseQuery()),
            ...FormationModel::findAll([], FormationModel::getAllLanguageBaseQuery()),
            ...CourseModel::findAll([], CourseModel::getAllLanguageBaseQuery())
        ];
        return Container::get('twig')->render('misc/sitemap.twig', [
            'host' => $_ENV['WP_HOME'],
            'pages' => $pages
        ]);
    }
}