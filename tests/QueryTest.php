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

    public function test_multiple_join()
    {
        $query = \Kogatana\Query::table('users')
            ->join('songs', array('users.id', '=', 'songs.user_id'))
            ->join('tracks', array('songs.id', '=', 'tracks.song_id'));
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nLEFT OUTER JOIN songs ON users.id = songs.user_id\n  LEFT OUTER JOIN tracks ON songs.id = tracks.song_id", $sql);
    }

    public function test_right_join()
    {
        $query = \Kogatana\Query::table('users')->join('songs', array('users.id', '=', 'songs.user_id'), 'right');
        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nRIGHT OUTER JOIN songs ON users.id = songs.user_id", $sql);
    }

    public function test_count_sql()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge');
        list($sql, $binds) = $query->count_sql();
        $this->assertEquals("SELECT\n  count(*)\nFROM\n  users\nWHERE\n  name = ?", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_to_subq()
    {
        $query = \Kogatana\Query::table('users')->eq('name', 'hoge');
        list($sql, $binds) = $query->to_subq();
        $this->assertEquals("(SELECT\n  *\nFROM\n  users\nWHERE\n  name = ?)", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_combination_using_subq()
    {
        $subq = \Kogatana\Query::table('songs')->eq('category', 'rock');
        list($subq_sql, $subq_binds) = $subq->to_subq();

        $query = \Kogatana\Query::table('users')->eq('sex', 'woman')->where_raw("EXISTS $subq_sql", $subq_binds);
        list($sql, $binds) = $query->to_sql();

        $expected = "SELECT\n  *\nFROM\n  users\nWHERE\n  sex = ?\n  AND EXISTS (SELECT\n  *\nFROM\n  songs\nWHERE\n  category = ?)";
        $this->assertEquals($expected, $sql);
        $this->assertEquals(array('woman', 'rock'), $binds);
    }
}
