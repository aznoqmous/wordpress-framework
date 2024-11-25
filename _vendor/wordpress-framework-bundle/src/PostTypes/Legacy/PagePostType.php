<?php

namespace Addictic\WordpressFrameworkBundle\PostTypes\Legacy;

use Addictic\WordpressFrameworkBundle\Annotation\PostType;
use Addictic\WordpressFrameworkBundle\Fields\Framework\Field;
use Addictic\WordpressFrameworkBundle\Fields\Framework\TextareaField;
use Addictic\WordpressFrameworkBundle\PostTypes\AbstractPostType;

/**
 * @PostType(name="page")
 */
class PagePostType extends AbstractPostType
{
    protected function configure()
    {
        $this->addSEO();
        $this->addDuplicateAction();

        add_filter("display_post_states", function($post_states, $post){
            if($post->ID == get_option("loginPage")) $post_states["login_page"] = "Page de connexion";
            if($post->ID == get_option("jobOffersPage")) $post_states["job_offers_page"] = "Page des offres d'emplois";
            if($post->ID == get_option("newsPage")) $post_states["news_page"] = "Page des actualités";
            if($post->ID == get_option("404page")) $post_states["404_page"] = "Page 404";
            if($post->ID == get_option("503page")) $post_states["503_page"] = "Page 503";
            return $post_states;
        }, 10, 2);
    }
}