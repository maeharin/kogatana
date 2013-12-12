<?php

class WhereTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_bind_nothing()
    {
        $where = new \Kogatana\Where('name = hoge');
        $this->assertEquals('name = hoge', $where->sql());
        $this->assertEquals(array(), $where->binds());
    }

    public function test_bind_single_value()
    {
        $where = new \Kogatana\Where('name = ?', 'hoge');
        $this->assertEquals('name = ?', $where->sql());
        $this->assertEquals(array('hoge'), $where->binds());
    }

    public function test_bind_multi_values()
    {
        $where = new \Kogatana\Where('name = ? AND sex = ?', array('hoge', 'man'));
        $this->assertEquals('name = ? AND sex = ?', $where->sql());
        $this->assertEquals(array('hoge', 'man'), $where->binds());
    }
}
