<?php

namespace Del\Repository;

use Codeception\TestCase\Test;
use Del\Collection\User as Users;
use Del\Entity\User;

class UserCollectionTest extends Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var Users
     */
    protected $users;

    protected function _before()
    {
        $this->users = new Users();
    }

    protected function _after()
    {
        unset($this->users);
    }


    public function testFindKeyReturnsFalseWhenNotInUsers()
    {
        $collection = new Users();
        $user = new User();
        $user->setId(1);
        $collection->append($user);
        $user = new User();
        $user->setId(2);
        $collection->append($user);
        $user = new User();
        $user->setId(3);
        $this->assertFalse($collection->findKey($user));
    }

    public function testFindById()
    {
        $collection = new Users();
        $user = new User();
        $user->setId(1);
        $collection->append($user);
        $user = new User();
        $user->setId(2);
        $collection->append($user);

        $user = $collection->findById(2);
        $this->assertInstanceOf('Del\Entity\User',$user);
    }

    public function testFindByIdReturnsFalse()
    {
        $collection = new Users();
        $user = new User();
        $user->setId(1);
        $collection->append($user);
        $user = new User();
        $user->setId(2);

        $this->assertFalse($collection->findById(911));
    }

    public function testUpdate()
    {
        $collection = new Users();
        $user = new User();
        $user->setId(1);
        $collection->append($user);
        $user = new User();
        $user->setId(2);
        $collection->append($user);
        $user = new User();
        $user->setId(3);
        $collection->append($user);
        $collection->first();
        $collection->next();
        $user = $collection->current(); //id 2
        $user->setEmail('a@b.com');
        $collection->update($user);
        $this->assertEquals('a@b.com',$collection[1]->getEmail());
        $user = new User();
        $user->setId(4);
        $this->expectException('LogicException');
        $collection->update($user);
    }

}
