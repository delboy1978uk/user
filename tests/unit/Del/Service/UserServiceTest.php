<?php

namespace DelTesting\Service;

use Codeception\Test\Unit;
use DateTime;
use Del\Entity\UserInterface;
use Del\Person\Entity\Person;
use Del\Person\Service\PersonService;
use Del\Criteria\UserCriteria;
use Del\Entity\EmailLink;
use Del\Entity\User;
use Del\Exception\EmailLinkException;
use Del\Exception\UserException;
use Del\Repository\EmailLink as EmailLinkRepository;
use Del\Repository\UserRepository;
use Del\Service\UserService;
use Del\Value\User\State;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;

class UserServiceTest extends Unit
{
    private MockObject $userRepo;
    private MockObject $emailLinkRepo;
    private UserService $svc;
    private UserInterface $user;
    private EmailLink $link;

    protected function _before()
    {
        $this->link = new EmailLink();
        $this->link->setToken('XXXXX');
        $this->link->setExpiryDate(new DateTime('+7 days'));
        $user = new User();
        $user->setId(6);
        $user->setEmail('man@work.com');
        $user->setPerson(new Person());
        $this->user = $user;
        $this->link->setUser($user);
        $this->userRepo = $this->makeEmpty(UserRepository::class, [
            'save' => $user,
        ]);
        $this->emailLinkRepo = $this->makeEmpty(EmailLinkRepository::class, [
            'save' => $this->link,
        ]);
        $em = $this->makeEmpty(EntityManager::class, [
            'merge' => $this->link
        ]);
        $map = [
            [User::class, $this->userRepo],
            [EmailLink::class, $this->emailLinkRepo],
        ];
        $em->method('getRepository')->willReturnMap($map);
        $personService = $this->makeEmpty(PersonService::class);
        $this->svc = new UserService($em, $personService);
        $this->user = $this->svc->changePassword($user,'testpass');
    }

    protected function _after()
    {
        unset($this->svc);
    }

    /**
     * @throws \Exception
     */
    public function testCreateFromArray()
    {
        $array = $this->getUserArray('testCreateFromArray');
        $user = $this->svc->createFromArray($array);
        $this->assertInstanceOf(User::class, $user);
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

    public function testSaveUser()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testSaveUser'));
        $user = $this->svc->saveUser($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue(is_numeric($user->getId()));

        $user->setEmail('a@b.com');
        $user = $this->svc->saveUser($user);
        $this->assertEquals('a@b.com', $user->getEmail());
        $this->svc->deleteUser($user, true);
    }

    public function testFindUserById()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $user = $this->svc->findUserById(6);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('man@work.com', $user->getEmail());
        $this->svc->deleteUser($user, true);
    }

    public function testFindUserByEmail()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $user = $this->svc->createFromArray($this->getUserArray('testFindUserByEmail'));
        $this->svc->saveUser($user);
        $user = $this->svc->findUserByEmail('a@b.com');
        $this->assertInstanceOf(User::class, $user);
        $this->svc->deleteUser($user, true);
    }

    public function testGenerateEmailLink()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testGenerateEmailLink'));
        $user = $this->svc->saveUser($user);
        $link = $this->svc->generateEmailLink($user);
        $this->assertInstanceOf(EmailLink::class, $link);
        $this->svc->deleteEmailLink($link);
        $this->svc->deleteUser($user, true);
    }

    public function testFindEmailLink()
    {
        $this->emailLinkRepo->method('findByToken')->willReturn($this->link);
        $user = $this->svc->createFromArray($this->getUserArray('testFindEmailLink'));
        $user = $this->svc->saveUser($user);
        $link = $this->svc->generateEmailLink($user);
        $token = $link->getToken();
        $link = $this->svc->findEmailLink($user->getEmail(),$token);
        $this->assertInstanceOf('Del\Entity\EmailLink', $link);
        $this->svc->deleteEmailLink($link);
        $this->svc->deleteUser($user, true);
    }

    public function testFindEmailLinkThrowsWhenNotFound()
    {
        $this->expectException(EmailLinkException::class);
        $this->expectExceptionMessage(EmailLinkException::LINK_NOT_FOUND);
        $this->svc->findEmailLink('not@important.com','notfound');
    }

    public function testFindEmailLinkThrowsWhenWrongUser()
    {
        $this->emailLinkRepo->method('findByToken')->willReturn($this->link);
        $this->expectException(EmailLinkException::class);
        $this->expectExceptionMessage(EmailLinkException::LINK_NO_MATCH);
        $this->svc->findEmailLink('wrong@email.com', 'XXXXX');
    }

    public function testFindEmailLinkThrowsWhenExpired()
    {
        $link = new EmailLink();
        $link->setUser($this->user);
        $link->setToken('XXXXX');
        $link->setExpiryDate(new DateTime('-2 days'));
        $this->emailLinkRepo->method('findByToken')->willReturn($link);
        $this->expectException(EmailLinkException::class);
        $this->expectExceptionMessage(EmailLinkException::LINK_EXPIRED);
        $this->svc->findEmailLink('man@work.com', 'XXXXX');
    }

    public function testRegisterUser()
    {
        $this->userRepo->method('findByCriteria')->willReturn([]);
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '123456',
        ];
        $user = $this->svc->registerUser($form);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('pass@test.com',$user->getEmail());
        $this->assertEquals(State::STATE_UNACTIVATED, $user->getState()->getValue());
        $this->svc->deleteUser($user, true);
    }

    public function testRegisterUserThrowsInvalidArgumentException()
    {
        $this->expectException('InvalidArgumentException');
        $form = [];
        $this->svc->registerUser($form);
    }

    public function testRegisterUserThrowsOnWrongConfirm()
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::WRONG_PASSWORD);
        $form = [
            'email' => 'pass@test.com',
            'password' => '123456',
            'confirm' => '654321',
        ];
        $this->svc->registerUser($form);
    }

    public function testRegisterUserThrowsOnExisting()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::USER_EXISTS);
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
        $user = $this->svc->createFromArray($this->getUserArray('testChangePassword'));
        $user = $this->svc->changePassword($user,'testpass');
        $this->assertTrue($this->svc->checkPassword($user,'testpass'));
    }

    public function testFindByCriteria()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $criteria = new UserCriteria();
        $criteria->setEmail('man@work.com');
        $criteria->setRegistrationDate('1970-01-01');
        $criteria->setLastLoginDate('1970-01-01');
        $criteria->setState((string) State::STATE_UNACTIVATED);
        $user = $this->svc->findByCriteria($criteria)[0];
        $this->assertInstanceOf(User::class, $user);
    }

    public function testFindOneByCriteria()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $criteria = new UserCriteria();
        $criteria->setEmail('a@b.com');
        $criteria->setRegistrationDate('1970-01-01');
        $criteria->setLastLoginDate('1970-01-01');
        $criteria->setState((string) State::STATE_UNACTIVATED);
        $criteria->setOrderDirection(UserCriteria::ORDER_ASC);
        $criteria->setPagination(2, 5);
        $user = $this->svc->findOneByCriteria($criteria);
        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($criteria->hasOrderDirection());
        $this->assertEquals(UserCriteria::ORDER_ASC, $criteria->getOrderDirection());
    }

    public function testAuthenticate()
    {
        $this->user->setState(new State(State::STATE_ACTIVATED));
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $id = $this->svc->authenticate('man@work.com','testpass');
        $this->assertTrue(is_numeric($id));
    }

    public function testAuthenticateThrowsWhenNotFound()
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::USER_NOT_FOUND);
        $this->svc->authenticate('not@found.com','testpass');
    }

    public function testAuthenticateThrowsWhenUnactivated()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $this->user->setState(new State(State::STATE_UNACTIVATED));
        $this->emailLinkRepo->method('findByToken')->willReturn($this->link);
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::USER_UNACTIVATED);
        $this->svc->authenticate('man@work.com','testpass');
    }


    public function testAuthenticateThrowsWhenDisabled()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $this->user->setState(new State(State::STATE_DISABLED));
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::USER_DISABLED);
        $this->svc->authenticate('man@work.com','testpass');
    }

    public function testAuthenticateThrowsWhenBanned()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $this->user->setState(new State(State::STATE_BANNED));
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::USER_BANNED);
        $this->svc->authenticate('man@work.com','testpass');
    }

    public function testAuthenticateThrowsWhenWrongPassword()
    {
        $this->userRepo->method('findByCriteria')->willReturn([$this->user]);
        $this->user->setState(new State(State::STATE_ACTIVATED));
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(UserException::WRONG_PASSWORD);
        $this->svc->authenticate('man@work.com','oops');
    }

    public function testGetPersonService()
    {
        $user = $this->svc->createFromArray($this->getUserArray('testChangePassword'));
        $user = $this->svc->saveUser($user);
        $this->user = $user;
        $this->svc->changePassword($user,'testpass');
        $this->assertInstanceOf(PersonService::class, $this->svc->getPersonSvc());
    }

    public function testHasProfile()
    {
        $this->assertFalse($this->svc->hasProfile($this->user));
        $this->user->getPerson()->setFirstname('Arnold');
        $this->user->getPerson()->setLastname('Schwarzenegger');
        $this->assertTrue($this->svc->hasProfile($this->user));
    }

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
