<?php

declare(strict_types=1);

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

    public function getEntityPath(): string
    {
        return 'vendor/delboy1978uk/user/src/Entity';
    }

    public function registerConsoleCommands(Container $container): array
    {
        $userService = $container->get(UserService::class);
        $userCommand = new UserCommand($userService);

        return [$userCommand];
    }
}
