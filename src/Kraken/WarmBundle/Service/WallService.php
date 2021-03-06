<?php

namespace Kraken\WarmBundle\Service;

class WallService
{
    protected $instance;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance;
    }

    public function getThermalConductance()
    {
        $house = $this->instance->get()->getHouse();
        $totalWallSize = $house->getWallSize() / 100;
        $internalIsolation = $house->getInternalIsolationLayer();
        $externalIsolation = $house->getExternalIsolationLayer();
        $primaryMaterial = $house->getPrimaryWallMaterial();
        $secondaryMaterial = $house->getSecondaryWallMaterial();

        $thermalResistance = 0;

        if ($internalIsolation) {
            $totalWallSize -= ($internalIsolation->getSize() / 100);
            $thermalResistance += stristr($internalIsolation->getMaterial()->getName(), 'pustka')
                ? 0.18
                : ($internalIsolation->getSize() / 100) / $internalIsolation->getMaterial()->getLambda();
        }

        if ($externalIsolation) {
            $totalWallSize -= ($externalIsolation->getSize() / 100);
            $thermalResistance += ($externalIsolation->getSize() / 100) / $externalIsolation->getMaterial()->getLambda();
        }

        if ($house->getConstructionType() == 'traditional') {
            if ($secondaryMaterial) {
                $thermalResistance += (0.65 * $totalWallSize) / $primaryMaterial->getLambda();
                $thermalResistance += (0.35 * $totalWallSize) / $secondaryMaterial->getLambda();
            } else {
                $thermalResistance += $totalWallSize / $primaryMaterial->getLambda();
            }
        }

        return $thermalResistance > 0
            ? round(1 / $thermalResistance, 2)
            : 0;
    }

    public function getInternalWallThermalConductance()
    {
        $year = $this->instance->get()->getConstructionYear();

        if ($year < 1975) {
            $lambda = 0.6;
            $size = 0.3;
        } elseif ($year < 1995) {
            $lambda = 0.4;
            $size = 0.25;
        } else {
            $lambda = 0.2;
            $size = 0.2;
        }

        $wallResistance = $size / $lambda;

        return 1 / $wallResistance;
    }
}
