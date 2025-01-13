<?php

namespace Addictic\WordpressFramework\Controller;

use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;

class JobApiController extends AbstractController
{

    /**
     * @Route("/job/list", name="job_list")
     */
    public function getList()
    {
        return JobTaxonomyModel::findAll()->fetchRows();
    }
}