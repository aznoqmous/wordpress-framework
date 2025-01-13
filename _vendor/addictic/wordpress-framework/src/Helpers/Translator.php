<?php

namespace Addictic\WordpressFramework\Helpers;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

class Translator
{
    protected static ?Translator $instance = null;
    protected BaseTranslator $translator;

    protected $translationFolder = "translations";
    protected string $translationPath;

    public function __construct()
    {
        static::$instance = $this;
        $this->translationPath = __DIR__ . "/../../../../../{$this->translationFolder}";
        $this->translator = new BaseTranslator("fr");
        if(!is_admin() and function_exists("wpml_get_current_language")) $this->translator->setLocale(wpml_get_current_language());
        $this->load();
    }

    protected function getTranslationsFolders()
    {
        return array_slice(scandir($this->translationPath), 2);
    }

    protected function load()
    {
        $this->translator->addLoader("yaml", new YamlFileLoader());
        foreach ($this->getTranslationsFolders() as $domain) {
            $path = $this->translationPath . "/" . $domain;
            if (is_dir($path)) {
                foreach (array_slice(scandir($path), 2) as $file) {
                    if (is_file($path . "/" . $file)) {
                        $this->translator->addResource("yaml", $path . "/" . $file, $domain);
                    }
                }
            }
        }
    }

    public function has(string $message)
    {
        return $this->translator->trans($message) != $message;
    }

    public function trans(string $message)
    {
        return $this->translator->trans($message);
    }

    public function getTranslatedObjectId($object_id, $type)
    {
        $current_language = apply_filters('wpml_current_language', NULL);
        // if array
        if (is_array($object_id)) {
            $translated_object_ids = array();
            foreach ($object_id as $id) {
                $translated_object_ids[] = apply_filters('wpml_object_id', $id, $type, true, $current_language);
            }
            return $translated_object_ids;
        } // if string
        elseif (is_string($object_id)) {
            // check if we have a comma separated ID string
            $is_comma_separated = strpos($object_id, ",");

            if ($is_comma_separated !== FALSE) {
                // explode the comma to create an array of IDs
                $object_id = explode(',', $object_id);

                $translated_object_ids = array();
                foreach ($object_id as $id) {
                    $translated_object_ids[] = apply_filters('wpml_object_id', $id, $type, true, $current_language);
                }

                // make sure the output is a comma separated string (the same way it came in!)
                return implode(',', $translated_object_ids);
            } // if we don't find a comma in the string then this is a single ID
            else {
                return apply_filters('wpml_object_id', intval($object_id), $type, true, $current_language);
            }
        } // if int
        else {
            return apply_filters('wpml_object_id', $object_id, $type, true, $current_language);
        }
    }
}