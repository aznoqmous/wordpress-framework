<?php

namespace Addictic\WordpressFramework\Models;

class ModelCollection implements \Iterator
{

    protected $items = [];
    protected $index = 0;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function current():mixed
    {
        return isset($this->items[$this->index]) ? $this->items[$this->index] : null;
    }

    public function next():void
    {
        $this->index++;
    }

    public function key():mixed
    {
        return $this->index;
    }

    public function valid():bool
    {
        return isset($this->items[$this->index]);
    }

    public function rewind():void
    {
        $this->index = 0;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function add($item)
    {
        $this->items[] = $item;
    }

    public function each($callback)
    {
        foreach ($this->items as $item) $callback($item);
        return $this;
    }

    public function fetchEach($field)
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->{$field};
        }
        return $result;
    }

    public function fetchRows()
    {
        return $this->map(fn($item) => $item->row());
    }

    public function getModels()
    {
        return $this->items;
    }

    public function map($callback)
    {
        $results = [];
        foreach ($this->items as $key => $item) $results[] = $callback($item, $key);
        return $results;
    }

    public function find($callback){
        foreach ($this->items as $key => $item) {
            if($callback($item, $key)) return $item;
        }
    }
}