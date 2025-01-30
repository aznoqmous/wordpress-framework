<?php

namespace Addictic\WordpressFramework\Fields;

class ListField extends Field
{

    protected string $strTemplate = "list";

    public function render()
    {
        $this->getValue();


        $this->value = is_string($this->value) ? json_decode($this->value, true) : $this->value;

        $this->args['template'] = $this->getEntity("INDEX");

        if (is_array($this->value) && count($this->value)) {
            foreach ($this->value as $i => $values) {
                $this->args['entities'][] = $this->getEntity($i);
            }
        }

        $this->addAttribute("data-name", $this->name);

        return parent::render();
    }

    public function getEntity($wildcard = 0)
    {
        $entity = [];

        $fields = $this->getFields($wildcard);
        foreach ($fields as $field) {
            $entity[$field->name] = $field;
        }

        return $entity;
    }

    private function getFields($wildcard = 0)
    {
        $fields = [];
        foreach ($this->args['fields'] as $field) {
            $name = $field->name;
            $field = clone $field;
            $field->name = "{$this->name}[{$wildcard}][{$field->name}]";
            $field->parentType = $this->parentType;
            $field->parentName = $this->parentName;
            $field->labelKey = preg_replace("/(\d|INDEX|\[|\])/", ".", $field->labelKey);
            $field->labelKey = preg_replace("/\.+/", ".", $field->labelKey);
            if(isset($this->settingSection)) $field->settingSection = $this->settingSection;
            if (isset($this->value[$wildcard][$name])) $field->setValue($this->value[$wildcard][$name]);
            $fields[] = $field;
        }
        return $fields;
    }

    public function validate()
    {
        parent::validate();
        $value = $this->value;
        if(is_string($this->value)) $value = json_decode($this->value);
        if(!$value) return true;
        foreach ($value as $i => $values) {
            $fields = array_values($this->getEntity($i));
            $j = 0;
            foreach ($values as $name => $v) {
                $fields[$j]->setValue($v);
                if(!$fields[$j]->validate()) return false;
                $j++;
            }
        }
        return true;
    }

}