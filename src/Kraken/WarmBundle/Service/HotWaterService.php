<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Wall;

class HotWaterService
{
    protected $instance;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance->get();
    }

    public function isIncluded()
    {
        return $this->instance->getIncludeHotWater();
    }

    public function getPower()
    {
        return $this->getTankCapacity() >= 150 ? 4 : 3;
    }

    public function getTankCapacity()
    {
        $hotWaterDemand = [
            'shower' => 30,
            'shower_bath' => 50,
            'bath' => 70,
        ];

        $needs = isset($hotWaterDemand[$this->instance->getHotWaterUse()])
            ? $hotWaterDemand[$this->instance->getHotWaterUse()]
            : $hotWaterDemand['shower_bath'];

        return max(90, $this->instance->getHotWaterPersons() * $needs);
    }
}
