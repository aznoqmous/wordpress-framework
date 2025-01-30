<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\BlockManager;
use Addictic\WordpressFramework\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlockController extends AbstractController
{

    /**
     * @Route("/block/render", name="block_render", methods={"POST"})
     */
    public function renderBlock()
    {
        $request = Request::createFromGlobals();
        $blockName = $request->request->get("name");
        $attributes = $request->request->get("attributes") ? json_decode(stripslashes($request->request->get("attributes")), true) : [];

        $block = BlockManager::getInstance()->get($blockName);
        return $block ? $block->instance->render($attributes, "") : "Block \"$blockName\" not found";
    }

    /**
     * @Route("/block/render", name="block_render", methods={"GET"})
     */
    public function getBlock()
    {
        $request = Request::createFromGlobals();
        $blockName = $request->get("name");
        $attributes = $request->get("attributes") ? json_decode(stripslashes($request->get("attributes")), true) : [];
        $block = BlockManager::getInstance()->get($blockName);
        return $block ? $block->instance->render($attributes, "") : "Block \"$blockName\" not found";
    }
}