<?php

namespace Kraken\WarmBundle\Service;

class FloorsService
{
    private $instance;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance;
    }

    public function getInstance()
    {
        return $this->instance->get();
    }

    public function getHouse()
    {
        return $this->getInstance()->getHouse();
    }

    public function isBasementHeated()
    {
        return in_array(0, $this->getHouse()->getBuildingHeatedFloors());
    }

    public function isGroundFloorHeated()
    {
        return in_array(1, $this->getHouse()->getBuildingHeatedFloors());
    }

    public function isAtticHeated()
    {
        $atticFloor = $this->getHouse()->getBuildingFloors();

        if ($this->getHouse()->getBuildingRoof() == 'steep') {
            $atticFloor++;
        }

        return in_array($atticFloor, $this->getHouse()->getBuildingHeatedFloors());
    }

    public function hasUnheatedFloors()
    {
        return count($this->getHouse()->getBuildingHeatedFloors()) <= $this->getHouse()->getBuildingFloors();
    }

    public function getTopLabel()
    {
        if (!$this->isAtticHeated()) {
            return 'Strop';
        }

        return 'Dach';
    }

    public function getTopIsolationLabel()
    {
        if (!$this->isAtticHeated()) {
            return 'Izolacja stropu między poddaszem a piętrem niżej';
        }

        return 'Izolacja dachu';
    }

    public function getBottomLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Podłoga';
        }

        if ($this->isBasementHeated()) {
            return 'Piwnica';
        }

        if ($this->isGroundFloorHeated()) {
            return 'Podłoga parteru';
        }

        return 'Strop nad parterem';
    }

    public function getBottomIsolationLabel()
    {
        if ($this->isBasementHeated()) {
            return 'Izolacja podłogi piwnicy';
        }

        if ($this->isGroundFloorHeated()) {
            return 'Izolacja podłogi parteru';
        }

        return 'Izolacja stropu nad parterem';
    }
}
