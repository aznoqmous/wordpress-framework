<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Helpers\WpmlHelper;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;

class RelationField extends Field
{
    protected string $strTemplate = "relation";

    public function render()
    {
        $post = get_post();
        $postId = null;
        $postType = null;

        if ($post) {
            $postId = $post->ID;
            $postType = $post->post_type;
        }

        $this->value = stripslashes($this->getValue());

        $this->args['limit'] = $this->args['limit'] ?? 10;

        if ($this->args['model']) {

            $this->args['model'] = $this->args['model']::getName();

            $value = json_decode($this->getValue()) ?? [];

            if (is_integer($value)) $value = [$value];
            $value = array_filter($value);

            $modelClass = AbstractPostTypeModel::getClassByName($this->args['model']);

            if ($value) {
                /** Translated taxonomies relations */
                if (WpmlHelper::isTranslatedPostType($this->args['model'])) {
                    $items = $modelClass::findBySourceLanguageIds($value);
                    $items = $items->getModels();
                    $this->applyRenderCallback($items);
                    $this->args['items'] = $items;
                }
                /** Basic relations  */
                else if (WpmlHelper::isTranslatedTaxonomy($this->args['model'])) {
                    $items = $modelClass::findBySourceLanguageIds($value);
                    $items = $items->getModels();
                    $this->applyRenderCallback($items);
                    $this->args['items'] = $items;
                }
                /** Translated posts relations */
                else if ($items = $modelClass::findByIds($value)) {
                    $items = $items->getModels();
                    $this->applyRenderCallback($items);
                    $this->args['items'] = $items;
                }
            }

            $optionsExcludedIds = $value;
            if ($postType == $this->args['model']) {
                $this->args['excludeId'] = $postId;
                $optionsExcludedIds[] = $postId;
            }

            $this->addAttribute('data-identifier', $this->getIdentifier());

            $options = $modelClass::findByExcludeIds([], $optionsExcludedIds, ['limit' => $this->args['limit']]);

            $this->applyRenderCallback($options);
            $this->args['options'] = $options;
        }

        return parent::render();
    }

    public function applyRenderCallback($items)
    {
        if (!isset($this->args['callback'])) return;
        foreach ($items as $item) {
            $item->_html = $this->args['callback']($item);
        }
    }
}