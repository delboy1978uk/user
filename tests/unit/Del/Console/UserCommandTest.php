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

class UserTest extends CommandTest
{

    /**
     * @var UserCommand
     */
    protected $command;

    protected function _before()
    {
        $svc = ContainerService::getInstance();
        $config = new UserPackage();
        $svc->registerToContainer($config);
        $svc->getContainer();
        $this->command = new UserCommand();
    }

    protected function _after()
    {
        unset($this->command);
    }

    public function testResetPass()
    {
        $container = ContainerService::getInstance()->getContainer();
        /** @var PersonService $personSvc */
        $personSvc = $container['service.person'];
        /** @var UserService $userSvc */
        $userSvc = $container['service.user'];

        $person = new Person();
        $person->setFirstname('Derek');
        $person = $personSvc->savePerson($person);

        $user = new User();
        $user->setEmail('test@123.com')
            ->setLastLogin(new DateTime())
            ->setRegistrationDate(new DateTime())
            ->setPerson($person)
            ->setState(new State(State::STATE_ACTIVATED));

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