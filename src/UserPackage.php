<?php

namespace Del;

use Del\Common\Container\RegistrationInterface;
use Del\Person\PersonPackage;
use Del\Person\Service\PersonService;
use Del\Service\UserService;
use Barnacle\Container;
use Doctrine\ORM\EntityManager;

class UserPackage implements RegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c): void 
    {
        $personPackage = new PersonPackage();
        $personPackage->addToContainer($c);

        $function = function (Container $c) {
            $entityManager = $c->get(EntityManager::class);
            $personService = $c->get(PersonService::class);

            return new UserService($entityManager, $personService);
        };

        $c[UserService::class] = $c->factory($function);
    }

    public function getEntityPath(): string
    {
        return 'vendor/delboy1978uk/user/src/Entity';
    }

    public function hasEntityPath(): bool
    {
        return true;
    }
}
