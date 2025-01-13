<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Controller\AbstractController;
use Addictic\WordpressFramework\Helpers\QueryBuilder;
use Addictic\WordpressFramework\Models\CourseModel;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;
use Addictic\WordpressFramework\Models\ModelCollection;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;
use Symfony\Component\HttpFoundation\Request;

class FormationApiController extends AbstractController
{
    /**
     * @Route("/formation/taxonomies", name="formation_taxonomies", methods={"GET"})
     */
    public function getFormationTaxonomies()
    {
        return [
            'themes' => ThemeTaxonomyModel::findAll()->fetchRows(),
            'jobs' => JobTaxonomyModel::findAll()->fetchRows(),
        ];
    }

    /**
     * @Route("/formation/by_taxonomies", name="formation_by_taxonomies", methods={"POST"})
     */
    public function findByTaxonomies()
    {
        $request = Request::createFromGlobals();
        $taxonomies = explode(",", $request->request->get("taxonomies")) ?: [];
        return [
            'courses' => CourseModel::findByTaxonomies($taxonomies)->fetchRows(),
            'formations' => FormationModel::findByTaxonomies($taxonomies)->fetchRows()
        ];
    }

    /**
     * @Route("/formation/render_by_taxonomies", name="formation_render_by_taxonomies", methods={"POST", "GET"})
     */
    public function renderFormationsByTaxonomies()
    {
        $request = Request::createFromGlobals();
        $taxonomies = $request->get("taxonomies");
        $taxonomies = $taxonomies ? explode(",", $taxonomies) : [];
        $limit = $request->get("limit");
        $offset = $request->get("offset");

        $formations = FormationModel::findWithTaxonomies($taxonomies, ['metas' => ["level"],
            'order' => "theme ASC, level ASC",
            'limit' => $limit,
            'offset' => $offset
        ]);

        return array_values(array_map(fn($el) => $el->renderCard(), $formations ? $formations->getModels() : []));
    }

    /**
     * @Route("/course/render_by_taxonomies", name="course_render_by_taxonomies", methods={"POST"})
     */
    public function renderCoursesByTaxonomies()
    {
        $request = Request::createFromGlobals();
        $taxonomies = $request->request->get("taxonomies");
        $taxonomies = $taxonomies ? explode(",", $taxonomies) : [];
        $limit = $request->request->get("limit");
        $offset = $request->request->get("offset");

        $courses = CourseModel::findWithTaxonomies($taxonomies, [
            'order' => "theme ASC",
            'limit' => $limit,
            'offset' => $offset
        ]);

        return array_values(array_map(fn($el) => $el->renderCard(), $courses ? $courses->getModels() : []));
    }


    /**
     * @Route("/formation/search", name="formation_search", methods={"POST", "GET"})
     */
    public function search()
    {
        $request = Request::createFromGlobals();
        $query = $request->request->get("query") ?: $_GET['query'];

        $formations = FormationModel::searchPost($query);

        return $formations->map(fn($el) => $el->renderCard());
    }

    /**
     * @Route("/formation/{id}/render", name="formation_render_item")
     */
    public function renderItem($id)
    {
        $formation = FormationModel::findOneBy(["ID = $id"]);
        $formation->loadFields();
        return array_merge($formation->row(), ['card' => $formation->renderCardFull()]);
    }

    /**
     * @Route("/formation/{id}", name="formation_get_item")
     */
    public function getItem($id)
    {
        $formation = FormationModel::findOneBy(["ID = $id"]);
        $formation->loadFields();
        return $formation->row();
    }
}