<?php

namespace DelTesting\Console;

use DateTime;
use Del\Common\Config\DbCredentials;
use Del\Common\ContainerService;
use Del\Console\UserCommand;
use Del\Entity\User;
use Del\Person\Entity\Person;
use Del\Person\Service\PersonService;
use Del\Service\UserService;
use Del\UserPackage;
use Del\Value\User\State;

class UserCommandTest extends CommandTest
{

    /**
     * @var UserCommand
     */
    protected $command;

    protected function _before()
    {
        $this->command = new UserCommand();
    }

    protected function _after()
    {
        unset($this->command);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testResetPass()
    {
        $container = ContainerService::getInstance()->getContainer();
        /** @var PersonService $personSvc */
        $personSvc = $container[PersonService::class];
        /** @var UserService $userSvc */
        $userSvc = $container[UserService::class];

        $person = new Person();
        $person->setFirstname('Derek');
        $person = $personSvc->savePerson($person);

        $user = new User();
        $user->setEmail('test@123.com');
        $user->setLastLogin(new DateTime());
        $user->setRegistrationDate(new DateTime());
        $user->setPerson($person);
        $user->setState(new State(State::STATE_ACTIVATED));

        $user = $userSvc->changePassword($user, 'changeme'); // This saves the user too

        $command = new UserCommand();
        $output = $this->runCommand($command,[
            'email' => 'test@123.com',
            'newPassword' => 'testPass!'
        ]);

        $userSvc->deleteUser($user);
        $personSvc->deletePerson($person);

        $this->assertContains('Password changed for test@123.com', $output);
    }

    public function testNotFound()
    {
        $command = new UserCommand();
        $output = $this->runCommand($command,[
            'email' => 'nobody@home.com',
            'newPassword' => 'irrelevant'
        ]);

        $this->assertContains('No User Found.', $output);
    }
}
