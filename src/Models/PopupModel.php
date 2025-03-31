<?php

namespace App\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\DateHelper;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;
use Addictic\WordpressFramework\Models\Legacy\PageModel;
use Addictic\WordpressFramework\Models\ModelCollection;

class PopupModel extends AbstractPostTypeModel
{
    protected static $strName = "popup";

    public static function findActives($opts = []): ?ModelCollection
    {
        $popups = self::findAll([
            'metas' => ['start_date', 'end_date', 'included_pages', 'excluded_pages']
        ]);
        if (!$popups) return null;

        $page = PageModel::findActive();

        $popups = $popups->filter(fn($popup) => (
            (!$popup->start_date || DateHelper::fromFormat("Y-m-d", $popup->start_date) < time())
            && (!$popup->end_date || DateHelper::fromFormat("Y-m-d", $popup->end_date) > time())
            && (!$popup->included_pages || in_array($page->id, explode(",", $popup->included_pages)))
            && (!$popup->excluded_pages || !in_array($page->id, explode(",", $popup->excluded_pages)))
        ));

        return $popups->count() ? $popups : null;
    }

    public function getImage()
    {
        $image = $this->getValue("image");
        return $image ? AttachmentModel::findById($image) : null;
    }

    public function render()
    {
        $this->loadFields();
        $image = $this->getImage();
        return Container::get("twig")->render("popup/single.twig", array_merge($this->row(), [
            'image' => $image ? $image->guid : null,
            'content' => $this->parseContent()
        ]));
    }
}