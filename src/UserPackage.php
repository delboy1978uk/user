<?php

namespace Del;

use Del\Common\Container\RegistrationInterface;
use Del\Person\PersonPackage;
use Del\Service\UserService;
use Doctrine\ORM\EntityManager;
use Pimple\Container;

class UserPackage implements RegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        $personPackage = new PersonPackage();
        $personPackage->addToContainer($c);

        $function = function ($c) {
            $svc = new UserService($c);
            return $svc;
        };

        $c['service.user'] = $c->factory($function);
    }

    public function getEntityPath()
    {
        return 'vendor/delboy1978uk/user/src/Entity';
    }

    public function hasEntityPath()
    {
        return true;
    }

}