<?php

namespace Del\Config\Container;

use Del\Common\Container\RegistrationInterface;
use Del\Config\Container\Person as PersonPackage;
use Del\Repository\User as UserRepository;
use Del\Service\User as UserService;
use Doctrine\ORM\EntityManager;
use Pimple\Container;

class User implements RegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        $personPackage = new PersonPackage();
        $personPackage->addToContainer($c);
        $this->addUserRepository($c);
        $this->addUserService($c);
    }

    private function addUserRepository(Container $c)
    {
        $c['repository.user'] = $c->factory(function ($c) {
            /** @var EntityManager $em */
            $em = $c['doctrine.entity_manager'];
            /** @var UserRepository $repo */
            $repo = $em->getRepository('Del\Entity\User');
            return $repo;
        });
    }

    private function addUserService(Container $c)
    {
        $c['service.user'] = $c->factory(function ($c) {
            $svc = new UserService($c['doctrine.entity_manager'], $c['service.person']);
            return $svc;
        });
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