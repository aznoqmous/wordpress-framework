<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Fields\Field;
use Addictic\WordpressFramework\Fields\MetaBox;
use Addictic\WordpressFramework\Fields\TextareaField;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\RoutingHelper;
use Addictic\WordpressFramework\Models\Legacy\BlockModel;
use PostTypes\Columns;
use PostTypes\PostType as BasePostType;

abstract class AbstractPostType
{
    public $name = "post";
    public $icon = null;
    public $priority = 0;
    public $taxonomies = [];
    protected BasePostType $postType;
    protected Columns $columns;
    public $fields = [];

    private $defaultPatterns = [];

    public function register()
    {
        $this->postType = new BasePostType($this->getNames(), [], []);
        $this->postType->labels($this->getLabels());
        $this->columns = $this->postType->columns();

        if ($this->icon) $this->postType->icon($this->icon);
        if ($this->taxonomies) $this->postType->taxonomy(explode(",", $this->taxonomies));

        $this->configure();
        $this->postType->register();
        $this->registerDefaultPatterns();
    }

    public function getValue($key)
    {
        return get_post_meta($this->ID, $key);
    }

    public function getNames()
    {
        $translator = Container::get("translator");
        return [
            'name' => $this->name,
            'singular' => $translator->trans($this->name . ".singular"),
            'plural' => $translator->trans($this->name . ".plural"),
            'slug' => $translator->trans($this->name . ".slug"),
        ];
    }

    public function getLabels()
    {
        $translator = Container::get("translator");
        return [
            'name' => $translator->trans($this->name . '.plural'),
            'singular_name' => $translator->trans($this->name . '.singular'),
            'menu_name' => $translator->trans($this->name . '.menu_name'),
            'all_items' => $translator->trans($this->name . '.all_items'),
            'add_new' => $translator->trans('default.add_new'),
            'add_new_item' => $translator->trans('default.add_new_item'),
            'edit_item' => $translator->trans($this->name . '.singular'),
            'new_item' => $translator->trans('default.new_item'),
            'view_item' => $translator->trans('default.view_item'),
            'search_items' => $translator->trans('default.search_items'),
            'not_found' => $translator->trans('default.not_found'),
            'not_found_in_trash' => $translator->trans('default.not_found_in_trash'),
            'parent_item_colon' => $translator->trans($this->name . '.parent_item_colon'),
        ];
    }

    public function getName()
    {
        return $this->name;
    }

    public function addColumn($name, $opts = [])
    {
        $opts = array_merge([
            'sortable' => false
        ], $opts);

        $translator = Container::get("translator");

        $this->columns->add([
            $name => $translator->has("$this->name.$name.label") ? $translator->trans("$this->name.$name.label") : $translator->trans("$this->name.$name")
        ]);

        if ($opts['sortable']) $this->columns->sortable([
            $name => [$name, true]
        ]);

        $callback = function ($column, $post_id) use ($name) {
            $value = get_post_meta($post_id, $name);
            return $value ? $value[0] : null;
        };

        if (isset($opts['callback'])) $callback = $opts['callback'];

        $this->columns->populate($name, function ($column, $post_id) use ($callback) {
            $value = $callback($column, $post_id) ?? "-";
            echo $value;
        });

        return $this;
    }

    public function removeColumn($name)
    {
        $this->columns->hide([$name]);
        return $this;
    }

    public function addMetabox($name, $args = []): MetaBox
    {
        $args = array_merge([
            'title' => Container::get("translator")->trans("$this->name.$name")
        ], $args);
        return MetaBox::create($name, $args, $this);
    }

    public function addField($field)
    {
        $this->fields[$field->name] = $field;
    }

    public function removeFromMenu()
    {
        add_action("admin_menu", function () {
            remove_menu_page("edit.php?post_type=" . $this->name);
        });
    }

    public function removeNewPostTypeButton()
    {
        add_action("admin_menu", function () {
            remove_submenu_page(
                "edit.php?post_type=" . $this->name,
                "post-new.php?post_type=" . $this->name
            );
        });
    }

    public function addSubmenu($parentSlug, $position = null)
    {
        $postType = $this->postType;
        add_action("admin_menu", function () use ($postType, $parentSlug, $position) {
            add_submenu_page(
                $parentSlug,
                $postType->slug,
                $postType->singular,
                "manage_options",
                "edit.php?post_type=" . $this->name,
                null,
                $position
            );
        });
    }

    abstract protected function configure();

    public function setOptions($arrOptions = [])
    {
        $this->postType->modifyPostType($arrOptions, $this->name);
    }

    private function registerDefaultPatterns()
    {
        $patterns = $this->defaultPatterns;
        add_filter("default_content", function ($content, $post) use ($patterns) {
            if ($post->post_type === $this->name) {
                $patterns = BlockModel::findByIds($patterns);
                if ($patterns) foreach ($patterns as $pattern) {
                    $content .= $pattern->render();
                }
            }
            return $content;
        }, 10, 2);
    }

    public function addDefaultPattern($patternId)
    {
        if (!$patternId) return;
        $this->defaultPatterns[] = $patternId;
    }

    public function addDefaultPatterns($patterns = [])
    {
        $this->defaultPatterns = array_merge($this->defaultPatterns, $patterns);
    }

    public function addSaveCallback($callback)
    {
        # avoid infinite calls loop when using wp_update_post
        $key = microtime() . $this->postType->name;
        $GLOBALS[$key] = false;
        $postType = $this->postType->name;
        add_action("save_post", function ($post_id) use ($callback, $postType, $key) {
            global $post;
            if(!$post) $post = get_post($post_id);
            if (!$post) return;
            if ($post->post_type != $postType) return;
            if ($GLOBALS[$key]) return;
            $GLOBALS[$key] = true;
            $callback($post_id, $post);
        });
    }

    public function addRowAction($callback)
    {
        if(!isset($_GET['post_type'])) return;

        $postType = $this->postType->name;
        add_filter("post_row_actions", function ($actions, $post) use ($postType, $callback
        ) {
            if ($_GET['post_type'] != $postType) return $actions;
            return $callback($actions, $post);
        }, 10, 2);
        add_filter("page_row_actions", function ($actions, $post) use ($postType, $callback
        ) {
            if ($_GET['post_type'] != $postType) return $actions;
            return $callback($actions, $post);
        }, 10, 2);
    }

    public function addSEO()
    {
        $this->addMetaBox("meta_legend")
            ->addField(new Field("meta_title"))
            ->addField(new TextareaField("meta_description"))
            ->apply();
    }

    public function addDuplicateAction()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        if (!isset($url['query'])) return;

        parse_str($url['query'], $output);
        if (isset($output['post_type']) && $output['post_type'] != $this->postType->name) return;

        if (isset($output['duplicate']) && $postId = $output['duplicate']) {
            add_action("init", function () use ($postId) {
                $this->clone(get_post($postId));
            });
        }
        $this->addRowAction(function ($buttons, $post) {
            $url = add_query_arg([
                'duplicate' => $post->ID
            ]);
            $buttons[] = "<a href=\"$url\">Duppliquer</a>";
            return $buttons;
        });
        return $this;
    }

    public function clone($post)
    {
        $newPost = [
            'post_author' => $post->post_author,
            'post_content' => $post->post_content,
            'post_content_filtered' => $post->post_content_filtered,
            'post_title' => $post->post_title . " (copie)",
            'post_excerpt' => $post->post_excerpt,
            'post_status' => $post->post_status,
            'post_type' => $post->post_type,
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_password' => $post->post_password,
            'post_name' => $post->post_name,
            'post_parent' => $post->post_parent,
            'menu_order' => $post->menu_order,
            'post_mime_type' => $post->post_mime_type,
        ];
        $new_post_id = wp_insert_post(wp_slash($newPost), true);

        // clone metas
        $metas = get_post_meta($post->ID);
        foreach ($metas as $key => $value) {
            update_post_meta($new_post_id, $key, $value[0]);
        }

        // clone taxonomies / paste from duplicate-post plugin, post-duplicator.php:220
        // Clear default category (added by wp_insert_post).
        \wp_set_object_terms($new_post_id, null, 'category');
        foreach ($this->postType->taxonomies as $taxonomy) {
            $post_terms = \wp_get_object_terms($post->ID, $taxonomy, ['orderby' => 'term_order']);
            $terms = [];
            $num_terms = \count($post_terms);
            for ($i = 0; $i < $num_terms; $i++) {
                $terms[] = $post_terms[$i]->slug;
            }
            \wp_set_object_terms($new_post_id, $terms, $taxonomy);
        }

        // Redirect
        $query = http_build_query([
            'post' => $new_post_id,
            'action' => "edit"
        ]);
        RoutingHelper::redirect(RoutingHelper::getAdminBase() . "/post.php?$query");
    }
}