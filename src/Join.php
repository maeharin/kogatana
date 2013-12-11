<?php

namespace Kogatana;

class Join
{
    protected $table_name;
    protected $condition;
    protected $join_type;

    public function __construct($table_name, $condition, $join_type)
    {
        $this->table_name = $table_name;
        $this->condition = $condition;
        $this->join_type = $join_type;
    }

    public function sql()
    {
        $sql = "";

        switch ($this->join_type) {
            case 'inner': 
                $sql .= "INNER JOIN ";
                break;
            case 'right':
                $sql .= "RIGHT OUTER JOIN ";
                break;
            default:
                $sql .= "LEFT OUTER JOIN ";
                break;
        }

        $sql .= $this->table_name . " ON ";
        $sql .= $this->condition[0] . " " . $this->condition[1] . " " . $this->condition[2];

        return $sql;
    }
}
