<?php

namespace Del\Repository;

use Del\Entity\User as UserEntity;
use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{
    /**
     * @param UserEntity $person
     * @return UserEntity
     */
    public function save(UserEntity $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }
    /**
     * @param UserEntity $person
     */
    public function delete(UserEntity $user)
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }
}
