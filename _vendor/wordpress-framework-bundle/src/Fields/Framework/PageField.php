<?php

namespace Addictic\WordpressFrameworkBundle\Fields\Framework;

use Addictic\WordpressFrameworkBundle\Models\Legacy\PageModel;

class PageField extends RelationField {
    public function render()
    {
        $this->setOption('model', PageModel::class);
        return parent::render();
    }
}