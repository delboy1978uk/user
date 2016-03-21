<?php

namespace Del\Entity;

use DateTime;
use Del\Person\Entity\Person;
use Del\Value\User\State;

class UserTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var User
     */
    protected $user;

    protected function _before()
    {
        $this->user = new User();
    }

    protected function _after()
    {
        unset($this->user);
    }

    public function testGetSetId()
    {
        $this->user->setId(100);
        $this->assertEquals(100,$this->user->getId());
    }

    public function testGetSetPerson()
    {
        $person = new Person();
        $person->setAka('Delboy');
        $this->user->setPerson($person);
        $this->assertInstanceOf('Del\Person\Entity\Person',$this->user->getPerson());
        $this->assertEquals('Delboy',$this->user->getPerson()->getAka());
    }

    public function testGetSetEmail()
    {
        $this->user->setEmail('delboy1978uk@gmail.com');
        $this->assertEquals('delboy1978uk@gmail.com',$this->user->getEmail());
    }

    public function testGetSetPassword()
    {
        $this->user->setPassword('[123456]');
        $this->assertEquals('[123456]',$this->user->getPassword());
    }

    public function testGetSetLastLoginDate()
    {
        $this->user->setLastLogin(new DateTime('2015-01-12'));
        $this->assertInstanceOf('DateTime',$this->user->getLastLoginDate());
        $this->assertEquals('2015-01-12',$this->user->getLastLoginDate()->format('Y-m-d'));
    }

    public function testGetSetRegistrationDate()
    {
        $this->user->setRegistrationDate(new DateTime('1970-01-01'));
        $this->assertInstanceOf('DateTime',$this->user->getRegistrationDate());
        $this->assertEquals('1970-01-01',$this->user->getRegistrationDate()->format('Y-m-d'));
    }

    public function testGetSetUserState()
    {
        $this->user->setState(new State(State::STATE_BANNED));
        $this->assertEquals(State::STATE_BANNED,$this->user->getState()->getValue());
    }

    public function testStateValueObject()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->user->setState(new State(666));
    }
}
