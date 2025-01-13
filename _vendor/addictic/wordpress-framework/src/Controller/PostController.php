<?php


namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;
use Addictic\WordpressFramework\Models\Legacy\PostModel;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{

    /**
     * @Route("/post/render", name="post_render", methods="POST")
     */
    public function renderPosts()
    {
        $request = Request::createFromGlobals();
        $offset = $request->request->get('offset');
        $limit = $request->request->get('limit');
        $category = intval($request->request->get('category'));


        $posts = $category ? PostModel::findBy(['category LIKE "%'.$category.'%"'], [
            'taxonomies' => ['category'],
            'offset' => $offset,
            'limit' => $limit,
            'order' => 'post_date DESC'
        ]) : PostModel::findAll([
            'offset' => $offset,
            'limit' => $limit,
            'order' => 'post_date DESC'
        ]);

        return $posts ? implode("", $posts->map(fn($post) => $post->renderItem())) : "";
    }
}