<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Entity\Wall;

class WallFactory
{
    public function getInternalWall(Calculation $calc)
    {
        $year = $calc->getConstructionYear();

        if ($year < 1975) {
            $lambda = 0.6;
            $size = 25;
        } elseif ($year < 1995) {
            $lambda = 1.6;
            $size = 20;
        } else {
            $lambda = 0.2;
            $size = 25;
        }

        $m = new Material();
        $m->setLambda($lambda);

        $l = new Layer();
        $l->setMaterial($m);
        $l->setSize($size);

        $wall = new Wall();
        $wall->setConstructionLayer($l);

        return $wall;
    }
}
