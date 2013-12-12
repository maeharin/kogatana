<?php

class JoinTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_left_join()
    {
        $join = new \Kogatana\Join('songs', array('users.id', '=', 'songs.user_id'), null);
        $this->assertEquals('LEFT OUTER JOIN songs ON users.id = songs.user_id', $join->sql());
    }

    public function test_inner_join()
    {
        $join = new \Kogatana\Join('songs', array('users.id', '=', 'songs.user_id'), 'inner');
        $this->assertEquals('INNER JOIN songs ON users.id = songs.user_id', $join->sql());
    }

    public function test_right_join()
    {
        $join = new \Kogatana\Join('songs', array('users.id', '=', 'songs.user_id'), 'right');
        $this->assertEquals('RIGHT OUTER JOIN songs ON users.id = songs.user_id', $join->sql());
    }
}
