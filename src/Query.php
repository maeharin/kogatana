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
        $parts = array();

        $parts[] = "SELECT\n  " . $this->select_clauses();

        $parts[] = "FROM\n  " . $this->from;

        if (! empty($this->joins)) {
            $parts[] = $this->join_clauses();
        }

        if (! empty($this->wheres)) {
            $parts[] = "WHERE\n  " . $this->where_clauses();
        }

        if (! empty($this->order)) {
            $parts[] = "ORDER BY\n  " . $this->order;
        }

        if (! empty($this->limit)) {
            $parts[] = "LIMIT\n  " . $this->limit;
        }

        if (! empty($this->offset)) {
            $parts[] = "OFFSET\n  " . $this->offset;
        }

        $res = array($this->build_query($parts), $this->binds);

        // for multiple use
        $this->_reset_binds();
        $this->select = null;

        return $res;
    }

    private function _reset_binds()
    {
        $this->binds = null;
    }

    /**
     * FIXME
     */
    public function to_subq()
    {
        $parts = array();

        $parts[] = "SELECT\n  " . $this->select_clauses();

        $parts[] = "FROM\n  " . $this->from;

        if (! empty($this->joins)) {
            $parts[] = $this->join_clauses();
        }

        if (! empty($this->wheres)) {
            $parts[] = "WHERE\n  " . $this->where_clauses();
        }

        if (! empty($this->order)) {
            $parts[] = "ORDER BY\n  " . $this->order;
        }

        if (! empty($this->limit)) {
            $parts[] = "LIMIT\n  " . $this->limit;
        }

        if (! empty($this->offset)) {
            $parts[] = "OFFSET\n  " . $this->offset;
        }

        $res = array("(" . $this->build_query($parts) . ")" , $this->binds);

        // for multiple use
        $this->_reset_binds();
        $this->select = null;

        return $res;
    }

    public function count_sql()
    {
        $this->select('count(*)');

        $parts = array();

        $parts[] = "SELECT\n  " . $this->select_clauses();

        $parts[] = "FROM\n  " . $this->from;

        if (! empty($this->joins)) {
            $parts[] = $this->join_clauses();
        }

        if (! empty($this->wheres)) {
            $parts[] = "WHERE\n  " . $this->where_clauses();
        }

        $res = array($this->build_query($parts), $this->binds);

        // for multiple use
        $this->_reset_binds();
        $this->select = null;

        return $res;
    }


    // ----------------------------------------------------------
    // private
    // ----------------------------------------------------------

    private function build_query($parts)
    {
        return implode("\n", $parts);
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
