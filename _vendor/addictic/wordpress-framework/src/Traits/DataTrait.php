<?php

namespace Addictic\WordpressFramework\Traits;

trait DataTrait
{
    protected $arrData = [];

    public function __get($key)
    {
        if (property_exists($this, $key)) return $this->{$key};
        return $this->arrData[$key];
    }

    public function __set($key, $value)
    {
        if (property_exists($this, $key)) $this->{$key} = $value;
        else $this->arrData[$key] = $value;
    }

    public function row()
    {
        return $this->arrData;
    }

    public function setData($arrData)
    {
        $this->arrData = array_merge($this->arrData, $arrData);
    }

}
