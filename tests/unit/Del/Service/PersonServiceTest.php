<?php

namespace Del\Repository;

use Codeception\TestCase\Test;
use DateTime;
use Del\Factory\CountryFactory;
use Del\Service\User as UserService;
use Del\Common\ContainerService;
use Del\Value\User\State;


class UserServiceTest extends Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var UserService
     */
    protected $svc;

    protected function _before()
    {
        $container = ContainerService::getInstance()
            ->getContainer();
        $this->svc = new UserService($container['doctrine.entity_manager'], $container['service.person']);
    }

    protected function _after()
    {
        unset($this->svc);
    }

    public function testCreateFromArray()
    {
        $array = $this->getUserArray();
        $user = $this->svc->createFromArray($array);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->assertEquals('a@b.com', $user->getEmail());
        $this->assertEquals('blah', $user->getPassword());
        $this->assertEquals('1970-01-01', $user->getRegistrationDate()->format('Y-m-d'));
        $this->assertEquals('1970-01-01', $user->getLastLoginDate()->format('Y-m-d'));
        $this->assertEquals(State::STATE_UNACTIVATED, $user->getState()->getValue());
        $this->assertInstanceOf('Del\Entity\Person', $user->getPerson());
    }

    public function testToArray()
    {
        $array = $this->getUserArray();
        $user = $this->svc->createFromArray($array);
        $array = $this->svc->toArray($user);

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('person', $array);
        $this->assertArrayHasKey('registrationDate', $array);
        $this->assertArrayHasKey('lastLoginDate', $array);
        $this->assertArrayHasKey('state', $array);
        $this->assertArrayHasKey('password', $array);
    }


    public function testSaveUser()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->saveUser($user);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->assertTrue(is_numeric($user->getId()));

        $user->setEmail('a@b.com');
        $user = $this->svc->saveUser($user);
        $this->assertEquals('a@b.com', $user->getEmail());
    }

    /**
     * @return array
     */
    private function getUserArray()
    {
        return [
            'email' => 'a@b.com',
            'lastLogin' => '1970-01-01',
            'registrationDate' => '1970-01-01',
            'state' => State::STATE_UNACTIVATED,
            'password' => 'blah'
        ];
    }

}
