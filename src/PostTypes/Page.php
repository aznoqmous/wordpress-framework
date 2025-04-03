<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\CheckboxField;
use Addictic\WordpressFramework\Fields\ColorField;
use Addictic\WordpressFramework\PostTypes\Legacy\PagePostType;

/**
 * @PostType(name="page")
 */
class Page extends PagePostType
{
    protected function configure()
    {
        parent::configure();

        $this->addMetabox("layout_legend")
            ->addField(new CheckboxField("white_header"))
            ->addField(new ColorField("background_color", [
                'colors' => [
                    "white",
                    "#06345D"
                ]
            ]))
            ->addField(new ColorField("text_color", [
                'colors' => [
                    "black",
                    "white"
                ]
            ]))
            ->apply();

//        add_filter("display_post_states", function($post_states, $post){
//            if($post->ID == get_option("loginPage")) $post_states["login_page"] = "Page de connexion";
//            if($post->ID == get_option("jobOffersPage")) $post_states["job_offers_page"] = "Page des offres d'emplois";
//            if($post->ID == get_option("newsPage")) $post_states["news_page"] = "Page des actualités";
//            if($post->ID == get_option("404page")) $post_states["404_page"] = "Page 404";
//            if($post->ID == get_option("503page")) $post_states["503_page"] = "Page 503";
//            return $post_states;
//        }, 10, 2);
    }
}