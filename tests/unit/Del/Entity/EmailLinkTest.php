<?php

namespace DelTesting\Entity;

use DateTime;
use Del\Entity\EmailLink;
use Del\Entity\User;

class UserTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var EmailLink
     */
    protected $link;

    protected function _before()
    {
        $this->link = new EmailLink();
    }

    protected function _after()
    {
        unset($this->link);
    }

    public function testGetSetId()
    {
        $this->link->setId(100);
        $this->assertEquals(100, $this->link->getId());
    }

    public function testGetSetExpiryDate()
    {
        $date = new DateTime('1970-01-02');
        $this->link->setExpiryDate($date);
        $this->assertInstanceOf('DateTime',$this->link->getExpiryDate());
        $this->assertEquals('1970-01-02', $this->link->getExpiryDate()->format('Y-m-d'));
    }

    public function testGetSetToken()
    {
        $this->link->setToken('blah');
        $this->assertEquals('blah', $this->link->getToken());
    }

    public function testGetSetUser()
    {
        $dave = new User();
        $this->link->setUser($dave);
        $this->assertInstanceOf('Del\Entity\User', $this->link->getUser());
    }
}
