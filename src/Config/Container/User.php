<?php

namespace Del\Config\Container;

use Del\Common\Container\RegistrationInterface;
use Del\Repository\User as UserRepository;
use Doctrine\ORM\EntityManager;
use Pimple\Container;

class User implements RegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        $c['repository.user'] = $c->factory(function ($c) {
            /** @var EntityManager $em */
            $em = $c['doctrine.entity_manager'];
            /** @var UserRepository $repo */
            $repo = $em->getRepository('Del\Entity\User');
            return $repo; });}
}