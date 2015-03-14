<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PerformanceRepository extends EntityRepository
{
    public function getCount()
    {
        $qb = $this->createQueryBuilder('p');
        $query = $qb->select($qb->expr()->count('p'))->getQuery();

        return $query->getSingleScalarResult();
    }
}
