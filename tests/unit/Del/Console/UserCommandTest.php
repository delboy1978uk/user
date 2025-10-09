<?php

namespace DelTesting\Console;

use Barnacle\Container;
use Codeception\Test\Unit;
use DateTime;
use Del\Common\ContainerService;
use Del\Console\ResetPasswordCommand;
use Del\Entity\User;
use Del\Person\Entity\Person;
use Del\Person\PersonPackage;
use Del\Person\Repository\PersonRepository;
use Del\Person\Service\PersonService;
use Del\Repository\UserRepository;
use Del\Service\UserService;
use Del\UserPackage;
use Del\Value\User\State;
use DelTesting\ContainerProvider;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UserCommandTest extends Unit
{
    private MockObject $personRepository;
    private MockObject $userRepository;
    private MockObject $entityManager;
    private UserService $userService;
    private Container $container;

    protected function _before()
    {
        $this->container = $container = ContainerProvider::getContainer();
        $this->personRepository = $this->makeEmpty(PersonRepository::class, [
            'save' => new Person()
        ]);
        $this->userRepository = $this->makeEmpty(UserRepository::class, [
            'save' => new User(),
        ]);
        $this->entityManager = $this->makeEmpty(EntityManager::class);
        $container[EntityManager::class] = $this->entityManager;
        $package = new UserPackage();
        $package->addToContainer($container);
        $this->userService = $container[UserService::class];
        $this->command = new ResetPasswordCommand($this->userService);
    }

    protected function _after()
    {
        unset($this->command);
    }

    public function testPackageMethods()
    {
        $package = new UserPackage();
        $this->assertEquals('vendor/delboy1978uk/user/src/Entity', $package->getEntityPath());
        $this->assertIsArray($package->registerConsoleCommands(ContainerProvider::getContainer()));
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testResetPass()
    {
        $this->entityManager->method('getRepository')
            ->willReturn($this->personRepository, $this->userRepository, $this->personRepository);
        $this->userRepository->method('findByCriteria')->willReturn([new User()]);
        $container = $this->container;
        /** @var PersonService $personSvc */
        $personSvc = $container->get(PersonService::class);
        /** @var UserService $userSvc */
        $userSvc = $container->get(UserService::class);
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
        $command = new ResetPasswordCommand($userSvc);
        $output = $this->runCommand($command,[
            'email' => 'test@123.com',
            'newPassword' => 'testPass!'
        ]);
        $userSvc->deleteUser($user);
        $personSvc->deletePerson($person);

        $this->assertStringContainsString('Password changed for test@123.com', $output);
    }

    public function testNotFound()
    {
        $this->userRepository->method('findByCriteria')->willReturn([]);
        $this->entityManager->method('getRepository')
            ->willReturn($this->userRepository);
        $command = new ResetPasswordCommand($this->userService);
        $output = $this->runCommand($command,[
            'email' => 'nobody@home.com',
            'newPassword' => 'irrelevant'
        ]);

        $this->assertStringContainsString('No User Found.', $output);
    }

    /**
     * @param Command $command
     * @return mixed|string
     */
    public function runCommand(Command $command, array $args)
    {
        $application = new Application();
        $application->add($command);
        $commandName = $command->getName();
        $args = array_merge(['command' => $commandName], $args);
        $command = $application->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        return $commandTester->getDisplay();
    }
}
