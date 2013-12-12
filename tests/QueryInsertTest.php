<?php

class QueryInsertTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_insert_sql()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->insert_sql(array('name' => 'hoge', 'sex' => 'man'));
        $this->assertEquals("INSERT INTO users\n  (name, sex)\nVALUES\n  (?, ?)", $sql);
        $this->assertEquals(array('hoge', 'man'), $binds);
    }

    public function test_insert_sql_with_single_value()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->insert_sql(array('name' => 'hoge'));
        $this->assertEquals("INSERT INTO users\n  (name)\nVALUES\n  (?)", $sql);
        $this->assertEquals(array('hoge'), $binds);
    }

    public function test_include_null()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->insert_sql(array('name' => 'hoge', 'sex' => null, 'age' => 30));
        $this->assertEquals("INSERT INTO users\n  (name, sex, age)\nVALUES\n  (?, ?, ?)", $sql);
        $this->assertEquals(array('hoge', null, 30), $binds);
    }
}
