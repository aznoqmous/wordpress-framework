<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Annotation\OldPostTypeManager;
use Addictic\WordpressFramework\Annotation\TaxonomyManager;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\WpmlHelper;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;
use Addictic\WordpressFramework\Settings\OptionsPage;
use Addictic\WordpressFramework\Settings\SettingSection;
use Addictic\WordpressFramework\Taxonomies\AbstractTaxonomy;
use Twig\TemplateWrapper;

class Field
{
    public string $name = "";
    public string $parentName = "";
    public string $labelKey;

    /**
     * Arguments
     */
    protected bool $required = false;
    protected $help;
    protected $args;

    protected string $strTemplate = "input";
    protected $value;
    protected TemplateWrapper $template;

    protected string $parentType = FieldParentTypes::SETTINGS;
    protected AbstractPostType $postType;
    protected AbstractTaxonomy $taxonomy;
    protected SettingSection $settingSection;

    public function __construct($name, $args = [])
    {
        $this->name = $name;
        $this->labelKey = $name;
        $this->help = $args['help'] ?? null;
        $this->required = $args['required'] ?? false;
        $this->template = Container::get("twig")->getTemplate("backend/fields/{$this->strTemplate}.twig");
        $this->args = array_merge($args, [
            'attributes' => []
        ]);

        if ($this->isTranslated() && !WpmlHelper::isDefaultLanguage()) {
            $this->name .= "_" . WpmlHelper::getCurrentLanguage();
        }
    }

    public function validate()
    {
//        if (is_array($this->value)) $this->value = json_encode($this->value);
//        if (is_object($this->value)) $this->value = json_encode($this->value);
        return true;
    }

    private function loadValue()
    {
        $value = $this->value;
        if (isset($this->postType)) {
            $value = get_post_meta(get_the_ID(), $this->name, true);
        } else if (isset($this->settingSection)) {
            $value = get_option($this->name);
        }
        return $value;
    }

    protected function getValue()
    {
        if (!$this->value) {
            $this->value = $this->loadValue();
        }
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setPostType(AbstractPostType $postType)
    {
        $this->parentName = $postType->name;
        $postType->addField($this);
        $this->postType = $postType;
        $this->parentType = FieldParentTypes::POST_TYPE;

        $field = $this;
        add_action("save_post", function () use ($field) {
            if (!isset($_POST[$this->name])) {
                return;
            }
            // Check if it's an autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            $field->setValue($_POST[$this->name]);

            if (!$field->validate()) {
                return;
            }

            update_post_meta(get_the_ID(), $field->name, $field->value);
        });
    }

    public function getPostType(): ?AbstractPostType
    {
        return $this->postType;
    }

    public function setTaxonomy(AbstractTaxonomy $taxonomy)
    {
        $field = $this;
        $this->parentName = $taxonomy->name;
        $this->taxonomy = $taxonomy;
        $this->parentType = FieldParentTypes::TAXONOMY;

        add_action("{$this->parentName}_add_form_fields", function () use ($field) {
            echo $field->render();
        });
        add_action("{$this->parentName}_edit_form_fields", function ($term) use ($field, $taxonomy) {
            $field->value = get_term_meta($term->term_id, $field->name, true);
            echo $field->render();
        });

        add_action("create_{$this->parentName}", function ($term_id) use ($field) {
            $field->setValue($_POST[$this->name]);
            if (!$field->validate()) {
                return;
            }
            if (isset($_POST[$field->name])) {
                update_term_meta($term_id, $field->name, $field->value);
            }
        });
        add_action("edited_{$this->parentName}", function ($term_id) use ($field) {
            $field->setValue($_POST[$this->name]);
            if (!$field->validate()) {
                return;
            }
            if (isset($_POST[$field->name])) {
                update_term_meta($term_id, $field->name, $field->value);
            }
        });
    }

    public function getTaxonomy(): ?AbstractTaxonomy
    {
        return $this->taxonomy;
    }

    public function setSettingSection(SettingSection $section)
    {
        $this->settingSection = $section;
        $this->parentName = $section->name;
        $this->parentType = FieldParentTypes::SETTINGS;
        if (is_admin() && isset($_POST[$this->name]) && isset($_GET['page']) && $_GET['page'] == $this->settingSection->page) {
            $this->setValue($_POST[$this->name]);
            if (!$this->validate()) return;
            update_option($this->name, $this->value);
        }
    }

    public function getSettingSection(): ?SettingSection
    {
        return $this->settingSection;
    }

    public function addAttribute($name, $value = null)
    {
        if ($value === false) {
            $value = "false";
        }
        if ($value === true) {
            $value = "true";
        }
        if ($value === "" || $value === null) {
            return;
        }
        $this->args['attributes'][] = "$name=$value";
    }

    public function setOption($key, $value)
    {
        $this->args[$key] = $value;
    }

    public function removeOption($key)
    {
        unset($this->args[$key]);
    }

    public function getOption($key)
    {
        return $this->args[$key];
    }

    public function render()
    {
        $translator = Container::get("translator");

        $label = $translator->has($this->parentName . "." . $this->labelKey . ".label");
        $help = "";

        if ($label) {
            $label = $translator->trans($this->parentName . "." . $this->labelKey . ".label");
            $help = $translator->trans($this->parentName . "." . $this->labelKey . ".help");
        } else {
            $label = $translator->trans($this->parentName . "." . $this->labelKey);
        }

        return $this->template->render(array_merge($this->args, [
            'name' => $this->name,
            'label' => $label,
            'help' => $help,
            'value' => $this->getValue(),
            'type' => $this->strTemplate,
            'field' => $this
        ]));
    }

    public function getIdentifier()
    {
        return "{$this->parentType}:{$this->parentName}:{$this->name}";
    }

    public static function getFieldByIdentifier($identifier)
    {
        $parts = explode(":", $identifier);
        $type = $parts[0];
        $parent = $parts[1];
        $fieldName = $parts[2];

        switch ($type) {
            case FieldParentTypes::POST_TYPE:
                $field = self::getField($fieldName, OldPostTypeManager::getInstance()->getPostType($parent)->instance->fields);
                break;
            case FieldParentTypes::TAXONOMY:
                $field = self::getField($fieldName, TaxonomyManager::getInstance()->getTaxonomy($parent)->instance->fields);
                break;
            case FieldParentTypes::SETTINGS:
                $field = self::getField($fieldName, OptionsPage::get($parent)->fields);
                break;
            default:
                break;
        }
        return $field;
    }

    private static function getField($fieldName, $fields)
    {
        $listFieldChildren = preg_replace("/.*?\[\d\]/", "", $fieldName);
        $listFieldChildren = preg_replace("/\[|\]/", "", $listFieldChildren);
        $fieldName = preg_replace("/\[\d\].*?$/", "", $fieldName);


        $field = $fields[$fieldName];
        if ($field instanceof ListField) {
            $field = $field->getOption("fields")[$listFieldChildren];
        }

        return $field;
    }

    public function isTranslated(): bool
    {
        return is_array($this->args) && isset($this->args['translated']) && $this->args['translated'];
    }
}
