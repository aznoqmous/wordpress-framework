<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\PostTypes\AbstractPostType;

class MetaBox
{

    protected array $fields = [];

    protected string $id;
    protected string $title;
    protected string $context;
    protected string $priority;
    protected AbstractPostType $model;

    public function __construct($args = [], $model = null)
    {
        $this->id = $args['id'];
        $this->title = $args['title'] ?? ucfirst($args['id']);
        $this->context = $args['context'] ?? "normal";
        $this->priority = $args['priority'] ?? "high";
        $this->model = $model;
    }

    public static function create($id, $args = [], $model = null)
    {
        return new static(array_merge([
            'id' => $id
        ], $args), $model);
    }

    public function addField(Field $field): self
    {
        $field->setPostType($this->model);
        $this->fields[] = $field;
        return $this;
    }

    public function row()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'context' => $this->context,
            'priority' => $this->priority
        ];
    }

    public function applyToPostType($postType)
    {
        $metabox = $this;
        add_action('add_meta_boxes', function () use ($metabox, $postType) {
            $data = $metabox->row();
            add_meta_box(
                $data['id'],
                $data['title'],
                function () use ($metabox) {
                    echo $metabox->render();
                },
                $postType,
                $data['context'],
                $data['priority'],
            );
        });

        return $this;
    }

    public function apply()
    {
        return $this->applyToPostType($this->model->name);
    }

    public function addMetaBox($name, $args = [])
    {
        return $this->model->addMetaBox($name, $args);
    }

    public function render()
    {
        return implode("", array_map(fn($field) => $field->render(), $this->fields));
    }
}