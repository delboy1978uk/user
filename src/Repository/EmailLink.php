<?php

namespace Del\Repository;

use Del\Entity\EmailLink as EmailLinkEntity;
use Doctrine\ORM\EntityRepository;

class EmailLink extends EntityRepository
{
    public function save(EmailLinkEntity $link):  EmailLinkEntity
    {
        $this->getEntityManager()->persist($link);
        $this->getEntityManager()->flush();

        return $link;
    }

    public function delete(EmailLinkEntity $link): void
    {
        $this->getEntityManager()->remove($link);
        $this->getEntityManager()->flush();
    }

    public function findByToken(string $token): ?EmailLinkEntity
    {
        $qb = $this->createQueryBuilder('el');

        $qb->where('el.token = :token');
        $qb->setParameter('token', $token);

        $query = $qb->getQuery();
        $result = $query->getResult();

        return count($result) ? $result[0] : null;
    }
}
