<?php

namespace Del\Repository;

use Codeception\TestCase\Test;
use DateTime;
use Del\Config\Container\User as UserPackage;
use Del\Criteria\UserCriteria;
use Del\Exception\EmailLinkException;
use Del\Exception\UserException;
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
        $svc = ContainerService::getInstance();
        $config = new UserPackage();
        $svc->registerToContainer($config);
        $container = $svc->getContainer();
        $this->svc = $container['service.user'];
    }

    protected function _after()
    {
        if(isset($this->link)) {
            $this->svc->deleteEmailLink($this->link);
        }
        if(isset($this->user)) {
            $this->svc->deleteUser($this->user);
        }
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
        $this->svc->deleteUser($user);
    }


    public function testFindUserById()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->saveUser($user);
        $id = $user->getID();
        $user = $this->svc->findUserById($id);
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->assertEquals('a@b.com', $user->getEmail());
        $this->svc->deleteUser($user);
    }


    public function testFindUserByEmail()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $this->svc->saveUser($user);
        $user = $this->svc->findUserByEmail('a@b.com');
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->svc->deleteUser($user);
    }


    public function testGenerateEmailLink()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->saveUser($user);
        $link = $this->svc->generateEmailLink($user);
        $this->assertInstanceOf('Del\Entity\EmailLink', $link);
        $this->svc->deleteEmailLink($link);
        $this->svc->deleteUser($user);
    }


    public function testFindEmailLink()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->saveUser($user);
        $link = $this->svc->generateEmailLink($user);
        $token = $link->getToken();
        $link = $this->svc->findEmailLink($user->getEmail(),$token);
        $this->assertInstanceOf('Del\Entity\EmailLink', $link);
        $this->svc->deleteEmailLink($link);
        $this->svc->deleteUser($user);
    }


    public function testFindEmailLinkThrowsWhenNotFound()
    {
        $this->setExpectedException('Del\Exception\EmailLinkException', EmailLinkException::LINK_NOT_FOUND);
        $this->svc->findEmailLink('not@important.com','notfound');
    }


    public function testFindEmailLinkThrowsWhenWrongUser()
    {
        $this->setExpectedException('Del\Exception\EmailLinkException', EmailLinkException::LINK_NO_MATCH);
        $this->user = $this->svc->createFromArray($this->getUserArray());
        $this->user = $this->svc->saveUser($this->user);
        $this->link = $this->svc->generateEmailLink($this->user);
        $token = $this->link->getToken();
        $this->svc->findEmailLink('wrong@email.com',$token);
    }


    public function testFindEmailLinkThrowsWhenExpired()
    {
        $this->setExpectedException('Del\Exception\EmailLinkException', EmailLinkException::LINK_EXPIRED);
        $user = $this->svc->createFromArray($this->getUserArray());
        $this->user = $this->svc->saveUser($user);
        $this->link = $this->svc->generateEmailLink($this->user,-8);
        $token = $this->link->getToken();
        $this->svc->findEmailLink($user->getEmail(),$token);
    }


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
        $this->svc->deleteUser($user);
    }


    public function testRegisterUserThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $form = [];
        $this->svc->registerUser($form);
    }


    public function testRegisterUserThrowsOnWrongConfirm()
    {
        $this->setExpectedException('Del\Exception\UserException', UserException::WRONG_PASSWORD);
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '654321',
        ];
        $this->svc->registerUser($form);
    }


    public function testRegisterUserThrowsOnExisting()
    {
        $this->setExpectedException('Del\Exception\UserException', UserException::USER_EXISTS);
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '123456',
        ];
        $this->user = $this->svc->registerUser($form);
        $this->svc->registerUser($form);
    }


    public function testChangePassword()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->saveUser($user);
        $user = $this->svc->changePassword($user,'testpass');
        $this->assertTrue($this->svc->checkPassword($user,'testpass'));
        $this->svc->deleteUser($user);
    }


    public function testFindByCriteria()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $this->svc->saveUser($user);
        $criteria = new UserCriteria();
        $criteria->setEmail('a@b.com')
        ->setRegistrationDate('1970-01-01')
        ->setLastLoginDate('1970-01-01')
        ->setState((string) State::STATE_UNACTIVATED);
        $user = $this->svc->findByCriteria($criteria)[0];
        $this->assertInstanceOf('Del\Entity\User', $user);
        $this->svc->deleteUser($user);
    }


    public function testAuthenticate()
    {
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_ACTIVATED));
        $user = $this->svc->saveUser($user);
        $this->assertEquals(State::STATE_ACTIVATED, $user->getState()->getValue());
        $id = $this->svc->authenticate('a@b.com','testpass');
        $this->assertTrue(is_numeric($id));
        $this->svc->deleteUser($user);
    }


    public function testAuthenticateThrowsWhenNotFound()
    {
        $this->setExpectedException('Del\Exception\UserException',UserException::USER_NOT_FOUND);
        $this->svc->authenticate('pass@test.com','testpass');
    }


    public function testAuthenticateThrowsWhenUnactivated()
    {
        $this->setExpectedException('Del\Exception\UserException',UserException::USER_UNACTIVATED);
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->changePassword($user,'testpass');
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','testpass');
    }


    public function testAuthenticateThrowsWhenDisabled()
    {
        $this->setExpectedException('Del\Exception\UserException',UserException::USER_DISABLED);
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_DISABLED));
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','testpass');
    }


    public function testAuthenticateThrowsWhenBanned()
    {
        $this->setExpectedException('Del\Exception\UserException',UserException::USER_BANNED);
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_BANNED));
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','testpass');
    }


    public function testAuthenticateThrowsWhenWrongPassword()
    {
        $this->setExpectedException('Del\Exception\UserException',UserException::WRONG_PASSWORD);
        $user = $this->svc->createFromArray($this->getUserArray());
        $user = $this->svc->changePassword($user,'testpass');
        $user->setState(new State(State::STATE_ACTIVATED));
        $this->user = $this->svc->saveUser($user);
        $this->svc->authenticate('a@b.com','oops');
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
