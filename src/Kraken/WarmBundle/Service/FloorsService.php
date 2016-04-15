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

        // attic under steep roof is not a regular floor actually
        if (!$this->getInstance()->isApartment() && $this->getHouse()->getBuildingRoof() == 'steep') {
            $atticFloor++;
        }

        return in_array($atticFloor, $this->getHouse()->getBuildingHeatedFloors());
    }

    public function getTotalFloorsNumber()
    {
        $house = $this->getHouse();
        $floorsNumber = max(1, $house->getBuildingFloors());

        if (!$this->getInstance()->isApartment() && $house->getBuildingRoof() == 'steep') {
            ++$floorsNumber;
        }

        if ($house->hasBasement()) {
            $floorsNumber++;
        }

        return $floorsNumber;
    }

    public function getHeatedFloorsNumber()
    {
        return count($this->getInstance()->getHouse()->getBuildingHeatedFloors());
    }

    public function hasUnheatedFloors()
    {
        return $this->getHeatedFloorsNumber() < $this->getTotalFloorsNumber();
    }

    public function getTopLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Strop';
        }

        if (!$this->isAtticHeated() && !$this->isGroundFloorHeated()) {
            return 'Strop';
        }

        if (!$this->isAtticHeated()) {
            return 'Strop - podłoga poddasza';
        }

        return 'Dach';
    }

    public function getTopIsolationLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Izolacja stropu';
        }

        if (!$this->isAtticHeated() && !$this->isGroundFloorHeated()) {
            return 'Izolacja stropu';
        }

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

        if (!$this->isGroundFloorHeated() && !$this->isAtticHeated()) {
            return 'Podłoga';
        }

        if (!$this->isGroundFloorHeated()) {
            return 'Strop nad parterem';
        }

        return 'Podłoga';
    }

    public function getBottomIsolationLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Izolacja podłogi';
        }

        if ($this->isBasementHeated()) {
            return 'Izolacja podłogi piwnicy';
        }

        if ($this->isGroundFloorHeated()) {
            return 'Izolacja podłogi parteru';
        }

        if (!$this->isGroundFloorHeated() && !$this->isAtticHeated()) {
            return 'Izolacja podłogi';
        }

        if (!$this->isGroundFloorHeated()) {
            return 'Izolacja stropu nad parterem';
        }

        return 'Izolacja podłogi';
    }
}
