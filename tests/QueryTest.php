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

    public function test_select_one_column()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge')->select('id');
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  id\nFROM\n  users\nWHERE\n  name = ?", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_multi_column()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge')->select(array('id', 'name'));
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  id, name\nFROM\n  users\nWHERE\n  name = ?", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_order()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge')->order('age DESC');
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  name = ?\nORDER BY\n  age DESC", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_limit_offset()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge')->limit(10)->offset(20);
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  name = ?\nLIMIT\n  10\nOFFSET\n  20", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_where_raw()
    {
        $query = \Kogatana\Query::table('users')->where_raw('name = ? AND sex = ?', array('hoge', 'man'));
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  name = ? AND sex = ?", $sql);
        $this->assertEquals(array('hoge', 'man'), $binds);
    }

    public function test_join()
    {
        $query = \Kogatana\Query::table('users')->join('songs', array('users.id', '=', 'songs.user_id'));
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nLEFT OUTER JOIN songs ON users.id = songs.user_id", $sql);
    }

    public function test_inner_join()
    {
        $query = \Kogatana\Query::table('users')->join('songs', array('users.id', '=', 'songs.user_id'), 'inner');
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nINNER JOIN songs ON users.id = songs.user_id", $sql);
    }
}
