<?php

namespace Kraken\WarmBundle\Service;

class HotWaterService
{
    protected $instance;

    public static $usages = [
        'shower' => 'w domu tylko prysznice',
        'shower_bath' => 'głównie prysznice, czasem wanna',
        'bath' => 'codziennie wanna dla każdego',
    ];

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

    public function getUsageLabel()
    {
        return self::$usages[$this->instance->getHotWaterUse()];
    }
}
