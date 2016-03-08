<?php

namespace Del\Repository;

use Del\Criteria\UserCriteria;
use Del\Entity\User as UserEntity;
use Doctrine\ORM\EntityRepository;

/**
 * @Entity(repositoryClass="Del\Repository\User")
 * @Table(name="User",uniqueConstraints={@UniqueConstraint(name="email_idx", columns={"email"})})
 */

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

        if($criteria->hasId()) {
            $qb->where('u.id = :id');
            $qb->setParameter('id', $criteria->getId());
            $criteria->setLimit(1);
        }

        if($criteria->hasEmail()) {
            $qb->where('u.email = :email');
            $qb->setParameter('email', $criteria->getEmail());
            $criteria->setLimit(1);
        }

        if($criteria->hasState()) {
            $qb->andWhere('u.state = :state');
            $qb->setParameter('state', $criteria->getState());
        }

        if($criteria->hasRegistrationDate()) {
            $qb->andWhere('u.registrationDate = :regdate');
            $qb->setParameter('regdate', $criteria->getRegistrationDate());
        }

        if($criteria->hasLastLoginDate()) {
            $qb->andWhere('u.lastLoginDate = :lastlogin');
            $qb->setParameter('lastlogin', $criteria->getLastLoginDate());
        }

        $criteria->hasOrder() ? $qb->addOrderBy('u.'.$criteria->getOrder(),$criteria->getOrderDirection()) : null;
        $criteria->hasLimit() ? $qb->setMaxResults($criteria->getLimit()) : null;
        $criteria->hasOffset() ? $qb->setFirstResult($criteria->getOffset()) : null;

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
