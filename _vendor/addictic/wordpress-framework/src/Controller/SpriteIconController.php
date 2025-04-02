<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\BlockManager;
use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\SpriteIcon;
use Symfony\Component\HttpFoundation\Request;

class SpriteIconController extends AbstractController
{

    /**
     * @Route("/sprite_icon/{name}/add", name="sprite_icon_add", methods={"POST"})
     */
    public function addIcon($name)
    {
        $spriteIcon = SpriteIcon::create($name);
        $spriteIcon->addHTML($_POST['icons']);
        $spriteIcon->saveDocument();
        return implode("", array_map(fn($id)=> Container::get("twig")->render("misc/sprite-icon.twig", [
            'name' => $name,
            'icon' => $id
        ]), $spriteIcon->getIcons()));
    }

    /**
     * @Route("/sprite_icon/{name}/remove/{icon}", name="sprite_icon_add", methods={"POST"})
     */
    public function removeIcon($name, $icon)
    {
        $spriteIcon = SpriteIcon::create($name);
        $spriteIcon->removeIcon($icon);
        $spriteIcon->saveDocument();
        return implode("", array_map(fn($id)=> Container::get("twig")->render("misc/sprite-icon.twig", [
            'name' => $name,
            'icon' => $id
        ]), $spriteIcon->getIcons()));
    }

    /**
     * @Route("/sprite/{name}/icons", name="sprite_icon_icons", methods={"GET"})
     */
    public function getIcons($name)
    {
        $spriteIcon = SpriteIcon::create($name);
        return $spriteIcon->getIcons();
    }
}