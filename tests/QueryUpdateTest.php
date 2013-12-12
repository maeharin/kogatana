<?php

class QueryUpdateTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_update_sql()
    {
        list($sql, $binds) = \Kogatana\Query::table('users')->eq('job', 'programmer')->update_sql(array('job' => 'engineer', 'age' => 20));
        $this->assertEquals("UPDATE\n  users\nSET\n  job = ?, age = ?\nWHERE\n  job = ?", $sql);
        $this->assertEquals(array('engineer', 20, 'programmer'), $binds);
    }

    public function test_query_should_be_reusable()
    {
        $query = \Kogatana\Query::table('users')->eq('job', 'programmer');

        list($update_sql, $update_binds) = $query->update_sql(array('job' => 'engineer', 'age' => 20));
        $this->assertEquals("UPDATE\n  users\nSET\n  job = ?, age = ?\nWHERE\n  job = ?", $update_sql);
        $this->assertEquals(array('engineer', 20, 'programmer'), $update_binds);

        list($sql, $binds) = $query->to_sql();
        $this->assertEquals("SELECT\n  *\nFROM\n  users\nWHERE\n  job = ?", $sql);
        $this->assertEquals(array('programmer'), $binds);
    }
}
