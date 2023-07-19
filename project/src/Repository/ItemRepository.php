<?php

namespace App\Repository;

use Bupy7\Doctrine\NestedSet\NestedSetRepositoryAbstract;

class ItemRepository extends NestedSetRepositoryAbstract
{
    public function findByCode(string $code)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb
            ->where($qb->expr()->like('d.code', ':code'))
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByCodePart(string $code)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb
            ->where($qb->expr()->like('d.code', ':code'))
            ->setParameter('code', $code . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByNamePart(string $code)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb
            ->where($qb->expr()->like('d.name', ':name'))
            ->setParameter('name', $code . '%')
            ->getQuery()
            ->getResult();
    }
}