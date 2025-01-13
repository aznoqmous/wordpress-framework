<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Fields\Field;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;
use Addictic\WordpressFramework\Models\AbstractTaxonomyModel;
use Symfony\Component\HttpFoundation\Request;

class RelationController extends AbstractController
{
    /**
     * @Route("/relation/search", name="relation_search", methods="POST,GET")
     */
    public function searchItem()
    {
        $request = Request::createFromGlobals();
        $body = json_decode($request->getContent());

        $query = $body->query;
        $model = $body->model;
        $exclude = $body->exclude ?? [];

        $modelClass = AbstractPostTypeModel::getClassByName($model);
        if(is_subclass_of($modelClass, AbstractTaxonomyModel::class)){
            $results = $modelClass::findByExcludeIds([
                "LOWER(name) LIKE \"$query%\""
            ], $exclude, ['limit' => 10]);
        }
        else {
            $results = $modelClass::findByExcludeIds([
                "LOWER(post_title) LIKE \"$query%\""
            ], $exclude, ['limit' => 10]);
        }

        if ($body->identifier && $field = Field::getFieldByIdentifier($body->identifier)) {
            $field->applyRenderCallback($results);
        }
        return $results->fetchRows();
    }
}