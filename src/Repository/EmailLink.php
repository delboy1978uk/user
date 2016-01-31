<?php

namespace Del\Repository;

use Del\Entity\EmailLink as EmailLinkEntity;
use Doctrine\ORM\EntityRepository;

class EmailLink extends EntityRepository
{
    /**
     * @param EmailLinkEntity $link
     * @return EmailLinkEntity
     */
    public function save(EmailLinkEntity $link)
    {
        $this->_em->merge($link);
        $this->_em->flush();
        return $link;
    }
    /**
     * @param EmailLinkEntity $link
     */
    public function delete(EmailLinkEntity $link)
    {
        $this->_em->remove($link);
        $this->_em->flush();
    }

    /**
     * @param string $token
     * @return EmailLinkEntity|null
     */
    public function findByToken($token)
    {
        $qb = $this->createQueryBuilder('el');

        $qb->where('el.token = :token');
        $qb->setParameter('token', $token);

        $query = $qb->getQuery();
        $result = $query->getResult();
        return count($result) ? $result[0] : null;
    }
}
