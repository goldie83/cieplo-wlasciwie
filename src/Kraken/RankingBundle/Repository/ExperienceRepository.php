<?php

namespace Kraken\RankingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kraken\RankingBundle\Entity\Boiler;

class ExperienceRepository extends EntityRepository
{
    public function findMostConfirmed(Boiler $boiler)
    {
        //TODO sort by confirmations
        return $this->createQueryBuilder('e')
            ->where('e.boiler = ?1')
            ->andWhere('e.accepted = true')
            ->setParameter(1, $boiler)
            ->getQuery()
            ->getResult();
    }
}
