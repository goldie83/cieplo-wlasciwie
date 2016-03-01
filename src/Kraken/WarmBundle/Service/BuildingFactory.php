<?php

namespace Kraken\WarmBundle\Service;

class BuildingFactory
{
    public function get(InstanceService $instance, VentilationService $ventilation, WallService $wall, WallFactory $wall_factory, DimensionsService $dimensions)
    {
        $calc = $instance->get();

        $building = new Building($instance, $ventilation, $wall, $wall_factory, $dimensions);

        return $building;
    }
}
