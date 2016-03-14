<?php

namespace Kraken\WarmBundle\Service;

class BuildingFactory
{
    public function get(InstanceService $instance, VentilationService $ventilation, WallService $wall, WallFactory $wall_factory, DimensionsService $dimensions, FloorsService $floors)
    {
        $calc = $instance->get();

        if ($calc->getBuildingType() == 'apartment') {
            return new Apartment($instance, $ventilation, $wall, $wall_factory, $dimensions, $floors);
        }

        return new Building($instance, $ventilation, $wall, $wall_factory, $dimensions, $floors);
    }
}
