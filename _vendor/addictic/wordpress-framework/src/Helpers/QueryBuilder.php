<?php

namespace Addictic\WordpressFramework\Helpers;

use Contao\Database;
use Contao\Model\Collection;
use Database\Result;

class QueryBuilder
{
    protected $fields = [];
    protected $conditions = [];
    protected $from = [];
    protected $joins = [];
    protected $groupBy = [];
    protected $orders = [];

    protected $offset = 0;
    protected $limit = -1;

    protected $values = [];

    private function __construct()
    {
    }

    public static function create()
    {
        return new static();
    }

    public function clone(): self
    {
        return clone $this;
    }

    public function __toString(): string
    {
        return $this->getQuery();
    }

    public function getQuery()
    {
        return
            'SELECT ' . (count($this->fields) ? implode(', ', array_unique($this->fields)) : "*")
            . ' FROM ' . (count($this->from) ? implode(', ', $this->from) : "wp_posts")
            . (count($this->joins) ? " " . implode(' ', $this->joins) : "")
            . (count($this->conditions) ? ' WHERE ' . implode(' AND ', $this->conditions) : " WHERE 1")
            . (count($this->groupBy) ? " GROUP BY " . implode(", ", $this->groupBy) : "")
            . (count($this->orders) ? " ORDER BY " . implode(", ", $this->orders) : "")
            . ($this->offset || $this->limit > 0 ? " LIMIT {$this->offset} , {$this->limit}" : "");
    }

    public function select(string ...$select): self
    {
        $this->fields = $select;
        return $this;
    }

    public function addSelect(string ...$select): self
    {
        $this->fields = [...$this->fields, ...$select];
        return $this;
    }

    public function where(string ...$where): self
    {
        $this->conditions = [...$this->conditions, ...$where];
        return $this;
    }

    public function whereIn($field, $values = [])
    {
        if (count($values)) $this->where("$field IN (" . implode(',', $values) . ")");
        return $this;
    }

    public function groupBy(string ...$fields): self
    {
        $this->groupBy = [...$this->groupBy, ...$fields];
        return $this;
    }

    public function from($table, ?string $alias = null): self
    {
        if ($table instanceof QueryBuilder) return $this->fromQuery($table, $alias);

        if ($alias === null) {
            $this->from[] = $table;
        } else {
            $this->from[] = "$table AS $alias";
        }
        return $this;
    }

    private function fromQuery($query, ?string $alias = null): self
    {
        if ($alias === null) {
            $this->from[] = "($query)";
        } else {
            $this->from[] = "($query) AS $alias";
        }
        return $this;
    }

    public function joinQuery($table, ?string $alias = null, string $joinType = null): self
    {
        if ($joinType) $joinType = "$joinType ";

        if ($alias === null) {
            $this->joins[] = "{$joinType}JOIN ({$table})";
        } else {
            $this->joins[] = "{$joinType}JOIN ({$table}) AS {$alias}";
        }
        return $this;
    }

    public function join($table, ?string $alias = null, string $joinType = null): self
    {
        if ($table instanceof QueryBuilder) return $this->joinQuery($table, $alias, $joinType);

        if ($joinType) $joinType = "$joinType ";

        if ($alias === null) {
            $this->joins[] = "{$joinType}JOIN {$table}";
        } else {
            $this->joins[] = "{$joinType}JOIN {$table} AS {$alias}";
        }
        return $this;
    }

    public function on(string ...$on): self
    {
        $this->joins[count($this->joins) - 1] = "{$this->joins[count($this->joins)-1]} ON " . implode(" AND ", $on);
        return $this;
    }

    public function orderBy(string ...$orderBy)
    {
        $this->orders = [...$this->orders, ...$orderBy];
        return $this;
    }

    public function offset($offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function limit($limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function set(string ...$orderBy)
    {
        $this->values = [...$this->values, ...$orderBy];
        return $this;
    }

    public function query()
    {
        global $wpdb;
        $wpdb->suppress_errors();
        $result = $wpdb->get_results($this->getQuery());
        if ($wpdb->last_error) {
            echo $this->__toString();
            throw new \Exception(
                implode("<br>", [
                    $wpdb->last_error
                ])
            );
        }
        return $result;
    }
}