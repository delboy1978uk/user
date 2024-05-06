<?php

namespace Del\Repository;

use Del\Criteria\UserCriteria;
use Del\Entity\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    private QueryBuilder $qb;

    public function save(UserInterface $user): UserInterface
    {
        if(!$user->getID()) {
            $this->getEntityManager()->persist($user);
        }
        $this->getEntityManager()->flush();

        return $user;
    }

    public function delete(UserInterface $user, $deletePerson = false): void
    {
        if($deletePerson) {
            $this->getEntityManager()->remove($user->getPerson());
        }

        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }


    private function checkCriteriaForId(UserCriteria $criteria):  UserCriteria
    {
        if($criteria->hasId()) {
            $this->qb->where('u.id = :id');
            $this->qb->setParameter('id', $criteria->getId());
            $criteria->setLimit(1);
        }

        return $criteria;
    }

    private function checkCriteriaForEmail(UserCriteria $criteria): UserCriteria
    {
        if($criteria->hasEmail()) {
            $this->qb->where('u.email = :email');
            $this->qb->setParameter('email', $criteria->getEmail());
            $criteria->setLimit(1);
        }

        return $criteria;
    }

    private function checkCriteriaForState(UserCriteria $criteria): void
    {
        if($criteria->hasState()) {
            $this->qb->andWhere('u.state = :state');
            $this->qb->setParameter('state', $criteria->getState());
        }
    }

    private function checkCriteriaForRegistrationDate(UserCriteria $criteria): void
    {
        if($criteria->hasRegistrationDate()) {
            $this->qb->andWhere('u.registrationDate = :regdate');
            $this->qb->setParameter('regdate', $criteria->getRegistrationDate());
        }
    }

    private function checkCriteriaForLastLoginDate(UserCriteria $criteria): void
    {
        if($criteria->hasLastLoginDate()) {
            $this->qb->andWhere('u.lastLoginDate = :lastlogin');
            $this->qb->setParameter('lastlogin', $criteria->getLastLoginDate());
        }
    }

    public function findByCriteria(UserCriteria $criteria): array
    {
        $this->qb = $this->createQueryBuilder('u');

        $criteria = $this->checkCriteriaForId($criteria);
        $criteria = $this->checkCriteriaForEmail($criteria);
        $this->checkCriteriaForState($criteria);
        $this->checkCriteriaForRegistrationDate($criteria);
        $this->checkCriteriaForLastLoginDate($criteria);

        $criteria->hasOrder() ? $this->qb->addOrderBy('u.'.$criteria->getOrder(),$criteria->getOrderDirection()) : null;
        $criteria->hasLimit() ? $this->qb->setMaxResults($criteria->getLimit()) : null;
        $criteria->hasOffset() ? $this->qb->setFirstResult($criteria->getOffset()) : null;

        $query = $this->qb->getQuery();

        return $query->getResult();
    }
}
