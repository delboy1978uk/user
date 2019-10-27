<?php

namespace DelTesting\Service;

use Codeception\TestCase\Test;
use Del\Person\Entity\Person;
use Del\Person\Service\PersonService;
use Del\UserPackage;
use Del\Criteria\UserCriteria;
use Del\Entity\EmailLink;
use Del\Entity\User;
use Del\Exception\EmailLinkException;
use Del\Exception\UserException;
use Del\Factory\CountryFactory;
use Del\Service\UserService;
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

    /** @var  User */
    protected $user;

    /** @var  EmailLink */
    protected $link;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function _before()
    {
        $container = ContainerService::getInstance()->getContainer();
        $this->svc = $container[UserService::class];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function _after()
    {
        if(isset($this->link)) {
            $this->svc->deleteEmailLink($this->link);
        }
        if(isset($this->user)) {
            $this->svc->deleteUser($this->user, true);
        }
        unset($this->svc);
    }

    /**
     * @throws \Exception
     */
    public function testCreateFromArray()
    {
        $array = $this->getUserArray('testCreateFromArray');
        $user = $this->svc->createFromArray($array);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->assertEquals('a@b.com', $user->getEmail());
        $this->assertEquals('testCreateFromArray', $user->getPassword());
        $this->assertEquals('1970-01-01', $user->getRegistrationDate()->format('Y-m-d'));
        $this->assertEquals('1970-01-01', $user->getLastLoginDate()->format('Y-m-d'));
        $this->assertEquals(State::STATE_UNACTIVATED, $user->getState()->getValue());
        $this->assertInstanceOf('Del\Person\Entity\Person', $user->getPerson());
    }

    /**
     * @throws \Exception
     */
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

    /**
     * @throws \Exception
     */
    public function testSaveUser()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testSaveUser'));
        $user = $this->svc->saveUser($user);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->assertTrue(is_numeric($user->getId()));

        $user->setEmail('a@b.com');
        $user = $this->svc->saveUser($user);
        $this->assertEquals('a@b.com', $user->getEmail());
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws \Exception
     */
    public function testFindUserById()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testFindUserById'));
        $user = $this->svc->saveUser($user);
        $id = $user->getID();
        $user = $this->svc->findUserById($id);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('a@b.com', $user->getEmail());
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws \Exception
     */
    public function testFindUserByEmail()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testFindUserByEmail'));
        $this->svc->saveUser($user);
        $user = $this->svc->findUserByEmail('a@b.com');
        $this->assertInstanceOf(User::class, $user);
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws \Exception
     */
    public function testGenerateEmailLink()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testGenerateEmailLink'));
        $user = $this->svc->saveUser($user);
        $link = $this->svc->generateEmailLink($user);
        $this->assertInstanceOf(EmailLink::class, $link);
        $this->svc->deleteEmailLink($link);
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws \Exception
     */
    public function testFindEmailLink()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testFindEmailLink'));
        $user = $this->svc->saveUser($user);
        $link = $this->svc->generateEmailLink($user);
        $token = $link->getToken();
        $link = $this->svc->findEmailLink($user->getEmail(),$token);
        $this->assertInstanceOf('Del\Entity\EmailLink', $link);
        $this->svc->deleteEmailLink($link);
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws EmailLinkException
     */
    public function testFindEmailLinkThrowsWhenNotFound()
    {
        $this->setExpectedException('Del\Exception\EmailLinkException', EmailLinkException::LINK_NOT_FOUND);
        $this->svc->findEmailLink('not@important.com','notfound');
    }


    /**
     * @throws \Exception
     */
    public function testFindEmailLinkThrowsWhenWrongUser()
    {
        $this->setExpectedException('Del\Exception\EmailLinkException', EmailLinkException::LINK_NO_MATCH);
        $this->user = $this->svc->createFromArray($this->getUserArray());
        $this->user = $this->svc->saveUser($this->user);
        $this->link = $this->svc->generateEmailLink($this->user);
        $token = $this->link->getToken();
        $this->svc->findEmailLink('wrong@email.com',$token);
    }


    /**
     * @throws \Exception
     */
    public function testFindEmailLinkThrowsWhenExpired()
    {
        $this->setExpectedException('Del\Exception\EmailLinkException', EmailLinkException::LINK_EXPIRED);
        $user = $this->svc->createFromArray($this->getUserArray('testFindEmailLinkThrowsWhenExpired'));
        $this->user = $this->svc->saveUser($user);
        $this->link = $this->svc->generateEmailLink($this->user,-8);
        $token = $this->link->getToken();
        $this->svc->findEmailLink($user->getEmail(),$token);
    }


    /**
     * @throws \Exception
     */
    public function testRegisterUser()
    {
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '123456',
        ];
        $user = $this->svc->registerUser($form);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->assertEquals('pass@test.com',$user->getEmail());
        $this->assertEquals(State::STATE_UNACTIVATED, $user->getState()->getValue());
        $this->svc->deleteUser($user, true);
    }


    /**
     * @throws \Exception
     */
    public function testRegisterUserThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $form = [];
        $this->svc->registerUser($form);
    }


    /**
     * @throws \Exception
     */
    public function testRegisterUserThrowsOnWrongConfirm()
    {
        $this->setExpectedException(UserException::class, UserException::WRONG_PASSWORD);
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '654321',
        ];
        $this->svc->registerUser($form);
    }


    /**
     * @throws \Exception
     */
    public function testRegisterUserThrowsOnExisting()
    {
        $this->setExpectedException(UserException::class, UserException::USER_EXISTS);
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '123456',
        ];
        $this->user = $this->svc->registerUser($form);
        $this->svc->registerUser($form);
    }

    /**
     * @throws \Exception
     */
    public function testChangePassword()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testChangePassword'));
        $user = $this->svc->saveUser($user);
        $user = $this->svc->changePassword($user,'testpass');
        $this->assertTrue($this->svc->checkPassword($user,'testpass'));
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws \Exception
     */
    public function testFindByCriteria()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testFindByCriteria'));
        $this->svc->saveUser($user);
        $this->user = $user;
        $criteria = new UserCriteria();
        $criteria->setEmail('a@b.com');
        $criteria->setRegistrationDate('1970-01-01');
        $criteria->setLastLoginDate('1970-01-01');
        $criteria->setState((string) State::STATE_UNACTIVATED);
        $user = $this->svc->findByCriteria($criteria)[0];
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->svc->deleteUser($user, true);
    }

    /**
     * @throws \Exception
     */
    public function testFindOneByCriteria()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testFindByCriteria'));
        $this->svc->saveUser($user);
        $criteria = new UserCriteria();
        $criteria->setEmail('a@b.com');
        $criteria->setRegistrationDate('1970-01-01');
        $criteria->setLastLoginDate('1970-01-01');
        $criteria->setState((string) State::STATE_UNACTIVATED);
        $user = $this->svc->findOneByCriteria($criteria);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->svc->deleteUser($user, true);
    }


    /**
     * @throws \Exception
     */
    public function testAuthenticate()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testAuthenticate'));
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_ACTIVATED));
        $user = $this->svc->saveUser($user);
        $this->assertEquals(State::STATE_ACTIVATED, $user->getState()->getValue());
        $id = $this->svc->authenticate('a@b.com','testpass');
        $this->assertTrue(is_numeric($id));
        $this->svc->deleteUser($user, true);
    }


    /**
     * @throws UserException
     */
    public function testAuthenticateThrowsWhenNotFound()
    {
        $this->setExpectedException(UserException::class,UserException::USER_NOT_FOUND);
        $this->svc->authenticate('not@found.com','testpass');
    }


    /**
     * @throws \Exception
     */
    public function testAuthenticateThrowsWhenUnactivated()
    {
        $this->setExpectedException(UserException::class,UserException::USER_UNACTIVATED);
        $user = $this->svc->createFromArray($this->getUserArray('testAuthenticateThrowsWhenUnactivated'));
        $user = $this->svc->changePassword($user,'testpass');
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','testpass');
    }


    /**
     * @throws \Exception
     */
    public function testAuthenticateThrowsWhenDisabled()
    {
        $this->setExpectedException(UserException::class,UserException::USER_DISABLED);
        $user = $this->svc->createFromArray($this->getUserArray('testAuthenticateThrowsWhenDisabled'));
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_DISABLED));
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','testpass');
    }


    /**
     * @throws \Exception
     */
    public function testAuthenticateThrowsWhenBanned()
    {
        $this->setExpectedException(UserException::class,UserException::USER_BANNED);
        $user = $this->svc->createFromArray($this->getUserArray('testAuthenticateThrowsWhenBanned'));
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_BANNED));
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','testpass');
    }


    /**
     * @throws \Exception
     */
    public function testAuthenticateThrowsWhenWrongPassword()
    {
        $this->setExpectedException(UserException::class,UserException::WRONG_PASSWORD);
        $user = $this->svc->createFromArray($this->getUserArray('testAuthenticateThrowsWhenWrongPassword'));
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_ACTIVATED));
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','oops');
    }



    /**
     * @throws \Exception
     */
    public function testGetPersonService()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testChangePassword'));
        $user = $this->svc->saveUser($user);
        $this->user = $user;
        $user = $this->svc->changePassword($user,'testpass');
        $this->assertInstanceOf(PersonService::class, $this->svc->getPersonSvc());
    }




    /**
     * @return array
     */
    private function getUserArray($functionName = 'getUserArray')
    {
        $person = new Person();
        $person->setFirstname($functionName);
        return [
            'person' => $person,
            'email' => 'a@b.com',
            'lastLogin' => '1970-01-01',
            'registrationDate' => '1970-01-01',
            'state' => State::STATE_UNACTIVATED,
            'password' => $functionName
        ];
    }

}
