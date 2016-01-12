<?php

namespace Del\Repository;

use Codeception\TestCase\Test;
use DateTime;
use Del\Common\ContainerService;
use Del\Config\Container\User as UserPackage;
use Del\Entity\Person;
use Del\Entity\User;
use Del\Repository\User as UserRepository;
use Del\Value\User\State;

class UserRepositoryTest extends Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var UserRepository
     */
    protected $db;

    protected function _before()
    {
        $svc = ContainerService::getInstance();
        $config = new UserPackage();
        $svc->registerToContainer($config);
        $container = $svc->getContainer();

        $this->db = $container['repository.user'];
    }

    protected function _after()
    {
        unset($this->db);
    }

    public function testPersistAndRetrieveUser()
    {
        $god = new Person();
        $god->setAka('God');
        $user = new User();
        $user->setEmail('god@work.com');
        $user->setRegistrationDate(new DateTime('1970-01-01'));
        $user->setLastLogin(new DateTime('2016-01-12'));
        $user->setPassword('praytothelord');
        $user->setState(new State(State::STATE_ACTIVATED));
        $user->setPerson($god);

        /** @var User $user */
        $user = $this->db->save($user);
        $id = $user->getId();
        $user = $this->db->find($id);

        $this->assertEquals($id,$user->getId());
        $this->assertEquals('God',$user->getPerson()->getAka());
        $this->assertEquals('god@work.com',$user->getEmail());
        $this->assertEquals('1970-01-01',$user->getRegistrationDate()->format('Y-m-d'));
        $this->assertEquals('2016-01-12',$user->getLastLoginDate()->format('Y-m-d'));
        $this->assertEquals('praytothelord',$user->getPassword());
        $this->assertEquals(State::STATE_ACTIVATED,$user->getState()->getValue());

        $this->db->delete($user);
        $this->db->delete($god);
        $this->assertNull($this->db->find($id));
    }

}
