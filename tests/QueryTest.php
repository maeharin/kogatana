<?php

class QueryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_eq()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge');
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  name = ?", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_where_raw()
    {
        $query = \Kogatana\Query::table('users')->where_raw('name = ? AND sex = ?', array('hoge', 'man'));
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  name = ? AND sex = ?", $sql);
        $this->assertEquals(array('hoge', 'man'), $binds);
    }
}

