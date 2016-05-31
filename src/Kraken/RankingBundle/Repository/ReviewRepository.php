<?php

namespace Kraken\RankingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kraken\RankingBundle\Entity\Boiler;

class ReviewRepository extends EntityRepository
{
    public function getAverageUserRating(Boiler $boiler)
    {
        //TODO
        return (float) $this->createQueryBuilder('d')
            ->select('d.value')
            ->where('d.site = ?1')
            ->orderBy('d.date', 'DESC')
            ->setParameter(1, $site)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
