<?php

namespace Del\Repository;

use Codeception\Test\Unit;
use Del\Common\ContainerService;

class UserRepositoryTest extends Unit
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var UserRepository
     */
    protected $db;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function _before()
    {
        $container = ContainerService::getInstance()->getContainer();
        $this->db = $container[UserRepository::class];
    }

    protected function _after()
    {
        unset($this->db);
    }

}
