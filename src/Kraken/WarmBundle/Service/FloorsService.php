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
        return in_array($this->getHouse()->getBuildingFloors(), $this->getHouse()->getBuildingHeatedFloors());
    }

    public function hasUnheatedFloors()
    {
        return count($this->getHouse()->getBuildingHeatedFloors()) <= $this->getHouse()->getBuildingFloors();
    }

    public function getTopLabel()
    {
        //TODO apartment!
        if (!$this->isAtticHeated()) {
            return 'Strop';
        }

        return 'Dach';
    }

    public function getTopIsolationLabel()
    {
        //TODO apartment!

        if (!$this->isAtticHeated()) {
            return 'Izolacja stropu między poddaszem a piętrem niżej';
        }

        return 'Izolacja dachu';
    }

    public function getBottomLabel()
    {
        //TODO apartment!
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
        //TODO apartment!
        if ($this->isBasementHeated()) {
            return 'Izolacja podłogi piwnicy';
        }

        if ($this->isGroundFloorHeated()) {
            return 'Izolacja podłogi parteru';
        }

        return 'Izolacja stropu nad parterem';
    }
}
