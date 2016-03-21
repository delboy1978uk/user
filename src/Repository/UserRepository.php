<?php

namespace Del\Repository;

use Del\Criteria\UserCriteria;
use Del\Entity\User;
use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return User
     */
    public function save(User $user)
    {
        if(!$user->getID()) {
            $this->_em->persist($user);
        }
        $this->_em->flush($user);
        $this->_em->flush($user->getPerson());
        return $user;
    }
    /**
     * @param User $user
     */
    public function delete(User $user, $deletePerson = false)
    {
        if($deletePerson) {
            $this->_em->remove($user->getPerson());
        }
        $this->_em->remove($user);
        $this->_em->flush($user);
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
