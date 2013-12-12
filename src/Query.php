<?php

namespace Kogatana;

class Query
{
    protected $select;
    protected $from;
    protected $wheres;
    protected $binds;
    protected $limit;
    protected $offset;
    protected $order;

    // init
    public static function table($table_name)
    {
        return new self($table_name);
    }

    public function __construct($table_name)
    {
        $this->from = $table_name;
    }

    public function join($table_name, $condition, $join_type = null)
    {
        $this->joins[] = new Join($table_name, $condition, $join_type);
        return $this;
    }

    public function eq($key, $bind)
    {
        $this->wheres[] = new Where("$key = ?", $bind);
        return $this;
    }

    public function where_raw($raw_str, $binds = array())
    {
        $this->wheres[] = new Where($raw_str, $binds);
        return $this;
    }

    public function select($columns = array())
    {
        if (empty($columns)) {
            return $this;
        }

        if (! is_array($columns)) {
            $this->select = (array)$columns;
            return $this;
        } else {
            $this->select = $columns;
            return $this;
        }

    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function to_sql()
    {
        $parts = $this->build_parts();
        $res = array($this->build_query($parts), $this->binds);
        $this->reset();
        return $res;
    }

    public function to_subq()
    {
        $parts = $this->build_parts();
        $res = array("(" . $this->build_query($parts) . ")" , $this->binds);
        $this->reset();
        return $res;
    }

    public function count_sql()
    {
        $this->select('count(*)');
        $parts = $this->build_parts('count');
        $res = array($this->build_query($parts), $this->binds);
        $this->reset();
        return $res;
    }

    public function insert_sql($attrs)
    {
        $keys = array_keys($attrs);
        $values = array_values($attrs);
        $place_holders = array_map(function($v) { return "?"; }, $values);

        $parts = array();
        $parts[] = "INSERT INTO $this->from";
        $parts[] = "  (" . implode(', ', $keys) . ")";
        $parts[] = "VALUES";
        $parts[] = "  (" . implode(', ', $place_holders) . ")";

        return array($this->build_query($parts), $values);
    }

    public function update_sql($attrs)
    {
        $values = array_values($attrs);

        $parts = array();
        $parts[] = "UPDATE";
        $parts[] = "  $this->from";
        $parts[] = "SET";
        $parts[] = "  ". implode(', ', array_map(function($attr) { return "$attr = ?"; }, array_keys($attrs)));
        if (! empty($this->wheres)) $parts[] = "WHERE\n  "    . $this->where_clauses();

        $sql = $this->build_query($parts);
        $binds = array_merge($values, (array)$this->binds);

        $this->reset();
        return array($sql, $binds);
    }

    public function delete_sql()
    {
        $parts = array();
        $parts[] = "DELETE FROM";
        $parts[] = "  $this->from";
        if (! empty($this->wheres)) $parts[] = "WHERE\n  "    . $this->where_clauses();
        if (! empty($this->limit))  $parts[] = "LIMIT\n  "    . $this->limit;
        $res = array($this->build_query($parts), $this->binds);
        $this->reset();
        return $res;
    }

    // ----------------------------------------------------------
    // private
    // ----------------------------------------------------------

    private function build_query($parts)
    {
        return implode("\n", $parts);
    }

    private function build_parts($type = null)
    {
        $parts = array();

        $parts[] = "SELECT\n  " . $this->select_clauses();
        $parts[] = "FROM\n  "   . $this->from;
        if (! empty($this->joins))  $parts[] = $this->join_clauses();
        if (! empty($this->wheres)) $parts[] = "WHERE\n  "    . $this->where_clauses();
        if (! empty($this->order))  $parts[] = "ORDER BY\n  " . $this->order;
        if ($type !== 'count') {
            if (! empty($this->limit))  $parts[] = "LIMIT\n  "    . $this->limit;
            if (! empty($this->offset)) $parts[] = "OFFSET\n  "   . $this->offset;
        }

        return $parts;
    }

    private function reset()
    {
        $this->binds = null;
        $this->select = null;
    }

    private function select_clauses()
    {
        if (is_null($this->select)) {
            return '*';
        }

        $select_clauses = array();

        return implode(", ", $this->select);
    }

    private function join_clauses()
    {
        $join_clauses = array();

        foreach($this->joins as $join) {
            $join_clauses[] = $join->sql();
        }

        return implode("\n  ", $join_clauses);
    }

    private function where_clauses()
    {
        $where_clauses = array();

        foreach($this->wheres as $where) {
            $where_clauses[] = $where->sql();

            if ($where->binds()) {
                foreach($where->binds() as $bind) {
                    $this->binds[] = $bind;
                }
            }
        }

        return implode("\n  AND ", $where_clauses);
    }
}
