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
        return count($this->getAllFloors());
    }

    public function getHeatedFloorsNumber()
    {
        return count($this->getInstance()->getHouse()->getBuildingHeatedFloors());
    }

    public function hasUnheatedFloors()
    {
        return $this->getHeatedFloorsNumber() < $this->getTotalFloorsNumber();
    }

    public function getAllFloors()
    {
        $house = $this->getHouse();
        $floorsNumber = max(1, $house->getBuildingFloors());
        $allFloors = [];

        if ($house->hasBasement()) {
            $allFloors[] = 0;
        }

        $i = 1;
        for (; $i <= $floorsNumber; $i++) {
            $allFloors[] = $i;
        }

        if (!$this->getInstance()->isApartment() && $house->getBuildingRoof() == 'steep') {
            $allFloors[] = $i;
        }

        return $allFloors;
    }

    public function getFirstFloorIndex()
    {
        $allFloors = $this->getAllFloors();

        return reset($allFloors);
    }

    public function getLastFloorIndex()
    {
        $allFloors = $this->getAllFloors();

        return end($allFloors);
    }

    public function getFirstHeatedFloorIndex()
    {
        $heatedFloors = $this->getInstance()->getHouse()->getBuildingHeatedFloors();

        return reset($heatedFloors);
    }

    public function getLastHeatedFloorIndex()
    {
        $heatedFloors = $this->getInstance()->getHouse()->getBuildingHeatedFloors();

        return end($heatedFloors);
    }

    public function getTopLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Strop';
        }

        $lastFloor = $this->getLastFloorIndex();
        $lastHeatedFloor = $this->getLastHeatedFloorIndex();

        if ($lastHeatedFloor == $lastFloor) {
            return 'Dach';
        }

        if ($lastHeatedFloor == $lastFloor - 1) {
            return 'Strop - podłoga poddasza';
        }

        return 'Strop';
    }

    public function getTopIsolationLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Izolacja stropu';
        }

        $lastFloor = $this->getLastFloorIndex();
        $lastHeatedFloor = $this->getLastHeatedFloorIndex();

        if ($lastHeatedFloor == $lastFloor) {
            return 'Izolacja dachu';
        }

        if ($lastHeatedFloor == $lastFloor - 1) {
            return 'Izolacja stropu między poddaszem a piętrem niżej';
        }

        return 'Izolacja stropu';
    }

    public function getBottomLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Podłoga';
        }

        $firstHeatedFloor = $this->getFirstHeatedFloorIndex();

        if ($firstHeatedFloor == 0) {
            return 'Piwnica';
        }

        if ($firstHeatedFloor == 1) {
            return 'Podłoga parteru';
        }

        if ($firstHeatedFloor == 2) {
            return 'Strop nad parterem';
        }

        return 'Podłoga';
    }

    public function getBottomIsolationLabel()
    {
        if ($this->getInstance()->isApartment()) {
            return 'Izolacja podłogi';
        }

        $firstHeatedFloor = $this->getFirstHeatedFloorIndex();

        if ($firstHeatedFloor == 0) {
            return 'Izolacja podłogi piwnicy';
        }

        if ($firstHeatedFloor == 1) {
            return 'Izolacja podłogi parteru';
        }

        if ($firstHeatedFloor == 2) {
            return 'Izolacja stropu nad parterem';
        }

        return 'Izolacja podłogi';
    }
}
