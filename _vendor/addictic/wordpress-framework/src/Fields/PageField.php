<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Models\Legacy\PageModel;

class PageField extends RelationField {
    public function render()
    {
        $this->setOption('model', PageModel::class);
        return parent::render();
    }
}