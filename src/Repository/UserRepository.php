<?php

namespace Del\Repository;

use Del\Criteria\UserCriteria;
use Del\Entity\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    /** @var QueryBuilder $qb */
    private $qb;

    /**
     * @param UserInterface $user
     * @return UserInterface
     * @throws \Exception
     */
    public function save(UserInterface $user)
    {
        if(!$user->getID()) {
            $this->_em->persist($user);
        }
        $this->_em->flush($user);
        $this->_em->flush($user->getPerson());
        return $user;
    }

    /**
     * @param UserInterface $user
     * @param bool $deletePerson
     * @throws \Exception
     */
    public function delete(UserInterface $user, $deletePerson = false)
    {
        if($deletePerson) {
            $this->_em->remove($user->getPerson());
        }
        $this->_em->remove($user);
        $this->_em->flush($user);
    }

    /**
     * @param UserCriteria $criteria
     * @return UserCriteria
     */
    private function checkCriteriaForId(UserCriteria $criteria)
    {
        if($criteria->hasId()) {
            $this->qb->where('u.id = :id');
            $this->qb->setParameter('id', $criteria->getId());
            $criteria->setLimit(1);
        }
        return $criteria;
    }

    /**
     * @param UserCriteria $criteria
     * @return UserCriteria
     */
    private function checkCriteriaForEmail(UserCriteria $criteria)
    {
        if($criteria->hasEmail()) {
            $this->qb->where('u.email = :email');
            $this->qb->setParameter('email', $criteria->getEmail());
            $criteria->setLimit(1);
        }
        return $criteria;
    }

    private function checkCriteriaForState(UserCriteria $criteria)
    {
        if($criteria->hasState()) {
            $this->qb->andWhere('u.state = :state');
            $this->qb->setParameter('state', $criteria->getState());
        }
    }

    private function checkCriteriaForRegistrationDate(UserCriteria $criteria)
    {
        if($criteria->hasRegistrationDate()) {
            $this->qb->andWhere('u.registrationDate = :regdate');
            $this->qb->setParameter('regdate', $criteria->getRegistrationDate());
        }
    }

    private function checkCriteriaForLastLoginDate(UserCriteria $criteria)
    {
        if($criteria->hasLastLoginDate()) {
            $this->qb->andWhere('u.lastLoginDate = :lastlogin');
            $this->qb->setParameter('lastlogin', $criteria->getLastLoginDate());
        }
    }

    /**
     * @param UserCriteria $criteria
     * @return array
     */
    public function findByCriteria(UserCriteria $criteria)
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
