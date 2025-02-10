<?php

namespace Addictic\WordpressFramework\Command;

use Addictic\WordpressFramework\Annotation\Command;

/**
 * @Command(name="permalinks:update")
 */
class PermalinkCommand
{
    public function __invoke()
    {
        echo "Flushing rewrite rules...\n";
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure( '/%postname%/' );
        flush_rewrite_rules();
        echo "Rewrite rules flushed successfully.";
    }

    /**
     * @Command(name="permalinks:set")
     */
    public function setPermalinkStructure($structure)
    {
        echo "Setting rewrite rules to  \"$structure\"\n";
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure($structure);
        $this->flush();
    }

    protected function flush(){
        echo "Flushing rewrite rules...\n";
        flush_rewrite_rules();
        echo "Rewrite rules flushed successfully.";
    }
}