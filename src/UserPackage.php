<?php

namespace Del;

use Barnacle\EntityRegistrationInterface;
use Barnacle\RegistrationInterface;
use Bone\Console\CommandRegistrationInterface;
use Del\Console\UserCommand;
use Del\Person\PersonPackage;
use Del\Person\Service\PersonService;
use Del\Service\UserService;
use Barnacle\Container;
use Doctrine\ORM\EntityManager;

class UserPackage implements RegistrationInterface, EntityRegistrationInterface, CommandRegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c): void
    {
        if (!$c->has(PersonService::class)) {
            $personPackage = new PersonPackage();
            $personPackage->addToContainer($c);
        }

        $entityManager = $c->get(EntityManager::class);
        $personService = $c->get(PersonService::class);
        $userService = new UserService($entityManager, $personService);
        $c[UserService::class] = $userService;
    }

    /**
     * @return string
     */
    public function getEntityPath(): string
    {
        return 'vendor/delboy1978uk/user/src/Entity';
    }

    /**
     * @param Container $container
     * @return array
     */
    public function registerConsoleCommands(Container $container): array
    {
        $userService = $container->get(UserService::class);
        $userCommand = new UserCommand($userService);

        return [$userCommand];
    }
}
