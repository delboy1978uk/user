<?php

namespace DelTesting\Criteria;

use DateTime;
use Del\Criteria\UserCriteria;

class UserCriteriaTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var UserCriteria
     */
    protected $criteria;

    protected function _before()
    {
        $this->criteria = new UserCriteria();
    }

    protected function _after()
    {
        unset($this->criteria);
    }

    public function testGetSetHasEmail()
    {
        $this->assertFalse($this->criteria->hasEmail());
        $this->criteria->setEmail('pass@test.com');
        $this->assertTrue($this->criteria->hasEmail());
        $this->assertEquals('pass@test.com', $this->criteria->getEmail());
    }

    public function testGetSetHasId()
    {
        $this->assertFalse($this->criteria->hasId());
        $this->criteria->setId(100);
        $this->assertTrue($this->criteria->hasId());
        $this->assertEquals(100, $this->criteria->getId());
    }

    public function testGetSetHasLastLoginDate()
    {
        $this->assertFalse($this->criteria->hasLastLoginDate());
        $this->criteria->setLastLoginDate('2016-02-27 01:53:38');
        $this->assertTrue($this->criteria->hasLastLoginDate());
        $this->assertEquals('2016-02-27 01:53:38', $this->criteria->getLastLoginDate());
    }

    public function testGetSetHasLastLimit()
    {
        $this->assertFalse($this->criteria->hasLimit());
        $this->criteria->setLimit(100);
        $this->assertTrue($this->criteria->hasLimit());
        $this->assertEquals(100, $this->criteria->getLimit());
    }

    public function testGetSetHasOffset()
    {
        $this->assertFalse($this->criteria->hasOffset());
        $this->criteria->setOffset(38);
        $this->assertTrue($this->criteria->hasOffset());
        $this->assertEquals(38, $this->criteria->getOffset());
    }

    public function testGetSetHasOrder()
    {
        $this->assertFalse($this->criteria->hasOrder());
        $this->criteria->setOrder(UserCriteria::ORDER_EMAIL);
        $this->assertTrue($this->criteria->hasOrder());
        $this->assertEquals(UserCriteria::ORDER_EMAIL, $this->criteria->getOrder());
    }

    public function testGetSetHasRegistrationDate()
    {
        $this->assertFalse($this->criteria->hasRegistrationDate());
        $this->criteria->setRegistrationDate('1978-02-17');
        $this->assertTrue($this->criteria->hasRegistrationDate());
        $this->assertEquals('1978-02-17', $this->criteria->getRegistrationDate());
    }

    public function testGetSetHasState()
    {
        $this->assertFalse($this->criteria->hasState());
        $this->criteria->setState(1);
        $this->assertTrue($this->criteria->hasState());
        $this->assertEquals(1, $this->criteria->getState());
    }
}
