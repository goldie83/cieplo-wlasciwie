<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Wall;

class WallService
{
    protected $instance;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance;
    }

    public function getThermalConductance()
    {
        $thermalResistance = 0;
        $house = $this->instance->get()->getHouse();
        $wallConstructionSize = $house->getWallSize()/100;
        $internalIsolation = $house->getInternalIsolationLayer();
        $externalIsolation = $house->getExternalIsolationLayer();
        $primaryMaterial = $house->getPrimaryWallMaterial();
        $secondaryMaterial = $house->getSecondaryWallMaterial();

        if ($internalIsolation) {
            $wallConstructionSize -= ($internalIsolation->getSize()/100);
            $thermalResistance += stristr($internalIsolation->getMaterial()->getName(), 'pustka')
                ? 0.18
                : ($internalIsolation->getSize()/100)/$internalIsolation->getMaterial()->getLambda();
        }

        if ($externalIsolation) {
            $wallConstructionSize -= ($externalIsolation->getSize()/100);
            $thermalResistance += ($externalIsolation->getSize()/100)/$externalIsolation->getMaterial()->getLambda();
        }

        if ($house->getConstructionType() == 'traditional') {
            if ($secondaryMaterial) {
                $thermalResistance += (0.65*$wallConstructionSize)/$primaryMaterial->getLambda();
                $thermalResistance += (0.35*$wallConstructionSize)/$secondaryMaterial->getLambda();
            } else {
                $thermalResistance += $wallConstructionSize/$primaryMaterial->getLambda();
            }
        }

        return $thermalResistance > 0
            ? round(1 / $thermalResistance, 2)
            : 0;
    }
}
