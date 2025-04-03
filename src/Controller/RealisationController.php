<?php

namespace App\Controller;

use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Controller\AbstractController;
use App\Models\RealisationModel;
use Symfony\Component\HttpFoundation\Request;

class RealisationController extends AbstractController
{

    /**
     * @Route("/realisation/list", name="realisation_list", methods={"POST", "GET"})
     */
    public function realisationList()
    {
        $request = Request::createFromGlobals();
        $options = $request->request->get('options');
        if(!$options) $realisations = RealisationModel::findAll();
        else $realisations = RealisationModel::findByTaxonomies(explode(",", $options));
        return $this->html(implode("", $realisations->map(fn($realisation)=> $realisation->renderItem())));
    }
}