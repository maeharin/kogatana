<?php

namespace Kogatana;

class Where
{
    protected $sql;
    protected $binds;

    public function __construct($sql, $binds = array())
    {
        $this->sql = $sql;

        if (is_null($binds)) {
            return ;
        }

        if (is_array($binds)) {
            $this->binds = $binds;
            return;
        } else {
            $this->binds = (array)$binds;
            return;
        }
    }

    public function sql()
    {
        return $this->sql;
    }

    public function binds()
    {
        return $this->binds;
    }
}
