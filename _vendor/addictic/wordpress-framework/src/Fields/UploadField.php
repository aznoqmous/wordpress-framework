<?php

namespace Addictic\WordpressFramework\Fields;

class UploadField extends Field
{
    protected string $strTemplate = "upload";

    public function render()
    {
        $this->args['files'] = [];
        $files = [];
        foreach(explode(",", $this->getValue()) as $id){
            if(!$id) continue;
            $files[] = [
                'id' => $id,
                'path' => wp_get_attachment_url($id)
            ];
        }
        $this->args['files'] = $files;

        $this->addAttribute("data-multiple", $this->args['multiple'] ?? false);
        $this->addAttribute("data-filetype", $this->args['fileType'] ?? null); // eg: image/jpeg,image/png,image...
        wp_enqueue_media();

        return parent::render();
    }
}