<?php

class QueryDeleteTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_delete_sql()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->delete_sql();
        $this->assertEquals("DELETE FROM\n  users", $sql);
        $this->assertEquals(null, $binds);
    }

    public function test_eq()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->eq('job', 'programmer')->delete_sql();
        $this->assertEquals("DELETE FROM\n  users\nWHERE\n  job = ?", $sql);
        $this->assertEquals(array('programmer'), $binds);
    }

    public function test_limit()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->limit(10)->delete_sql();
        $this->assertEquals("DELETE FROM\n  users\nLIMIT\n  10", $sql);
        $this->assertEquals(null, $binds);
    }

    public function test_select_and_delete()
    {
        $q = \Kogatana\Query::table('users')->eq('job', 'programmer')->eq('sex', 'man');

        list($sql, $binds) = $q->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  job = ?\n  AND sex = ?", $sql);
        $this->assertEquals(array('programmer', 'man'), $binds);

        list($delete_sql, $delete_binds) = $q->delete_sql();
        $this->assertEquals("DELETE FROM\n  users\nWHERE\n  job = ?\n  AND sex = ?", $delete_sql);
        $this->assertEquals(array('programmer', 'man'), $delete_binds);
    }
}
