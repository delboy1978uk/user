<?php

namespace Del\Repository;

use Del\Criteria\UserCriteria;
use Del\Entity\User as UserEntity;
use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{
    /**
     * @param UserEntity $user
     * @return UserEntity
     */
    public function save(UserEntity $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }
    /**
     * @param UserEntity $user
     */
    public function delete(UserEntity $user)
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    /**
     * @param UserCriteria $criteria
     * @return array
     */
    public function findByCriteria(UserCriteria $criteria)
    {
        $qb = $this->createQueryBuilder('u');

        if($criteria->hasEmail()) {
            $qb->where('u.email = :email');
            $qb->setParameter('email', $criteria->getEmail());
        }

        if($criteria->hasState()) {
            $qb->andWhere('u.state = :state');
            $qb->setParameter('state', $criteria->getState());
        }

        if($criteria->hasRegistrationDate()) {
            $qb->andWhere('u.registrationDate = :regdate');
            $qb->setParameter('registrationDate', $criteria->getRegistrationDate());
        }

        if($criteria->hasLastLoginDate()) {
            $qb->andWhere('u.lastLoginDate = :lastlogin');
            $qb->setParameter('lastlogin', $criteria->getLastLoginDate());
        }

        $criteria->hasOrder() ? $qb->addOrderBy($criteria->getOrder()) : null;
        $criteria->hasLimit() ? $qb->setMaxResults($criteria->getLimit()) : null;
        $criteria->hasOffset() ? $qb->setFirstResult($criteria->getOffset()) : null;

        $query = $qb->getQuery();
        return $query->getResult();
    }
}
