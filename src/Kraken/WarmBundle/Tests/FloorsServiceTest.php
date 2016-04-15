<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\FloorsService;
use Mockery;

class FloorsServiceTest extends \PHPUnit_Framework_TestCase
{
    protected function makeInstance(House $house)
    {
        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setBuildingType('single_house');

        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        return $instance;
    }

    public function testOneFloorNoBasementFlatRoof()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([1]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(1, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(false, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testOneFloorWithBasementFlatRoof()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([1]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(2, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testOneFloorHeatedBasementFlatRoof()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([0, 1]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(2, $fs->getTotalFloorsNumber());
        $this->assertEquals(2, $fs->getHeatedFloorsNumber());
        $this->assertEquals(true, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(false, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Piwnica', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi piwnicy', $fs->getBottomIsolationLabel());
    }


    public function testOneFloorNoBasementSteepRoofAtticNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([1]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(2, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(false, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Strop - podłoga poddasza', $fs->getTopLabel());
        $this->assertEquals('Izolacja stropu między poddaszem a piętrem niżej', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testOneFloorNoBasementSteepRoofAtticHeated()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([1, 2]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(2, $fs->getTotalFloorsNumber());
        $this->assertEquals(2, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(false, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testOneFloorWithBasementSteepRoofAtticNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([1]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(3, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(false, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Strop - podłoga poddasza', $fs->getTopLabel());
        $this->assertEquals('Izolacja stropu między poddaszem a piętrem niżej', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testOneFloorWithBasementSteepRoofAtticHeated()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([1, 2]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(3, $fs->getTotalFloorsNumber());
        $this->assertEquals(2, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testOneFloorBasementHeatedSteepRoofAtticHeated()
    {
        $house = new House();
        $house->setBuildingFloors(1);
        $house->setBuildingHeatedFloors([0, 1, 2]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(3, $fs->getTotalFloorsNumber());
        $this->assertEquals(3, $fs->getHeatedFloorsNumber());
        $this->assertEquals(true, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(false, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Piwnica', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi piwnicy', $fs->getBottomIsolationLabel());
    }


    public function testTwoFloorsNoBasementFlatRoofAtticNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(2);
        $house->setBuildingHeatedFloors([1]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(2, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(false, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Strop - podłoga poddasza', $fs->getTopLabel());
        $this->assertEquals('Izolacja stropu między poddaszem a piętrem niżej', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testTwoFloorsNoBasementFlatRoofGroundFloorNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(2);
        $house->setBuildingHeatedFloors([2]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(2, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(false, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Strop nad parterem', $fs->getBottomLabel());
        $this->assertEquals('Izolacja stropu nad parterem', $fs->getBottomIsolationLabel());
    }

    public function testTwoFloorsWithBasementFlatRoofGroundFloorNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(2);
        $house->setBuildingHeatedFloors([2]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(3, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(false, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Strop nad parterem', $fs->getBottomLabel());
        $this->assertEquals('Izolacja stropu nad parterem', $fs->getBottomIsolationLabel());
    }


    public function testTwoFloorsNoBasementSteepRoofAtticNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(2);
        $house->setBuildingHeatedFloors([1]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(3, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(false, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Strop - podłoga poddasza', $fs->getTopLabel());
        $this->assertEquals('Izolacja stropu między poddaszem a piętrem niżej', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga parteru', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi parteru', $fs->getBottomIsolationLabel());
    }

    public function testTwoFloorsNoBasementSteepRoofGroundFloorNotHeated()
    {
        $house = new House();
        $house->setBuildingFloors(2);
        $house->setBuildingHeatedFloors([3]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(false);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(3, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(false, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Strop nad parterem', $fs->getBottomLabel());
        $this->assertEquals('Izolacja stropu nad parterem', $fs->getBottomIsolationLabel());
    }

    public function testTwoFloorsWithBasementSteepRoofAllHeated()
    {
        $house = new House();
        $house->setBuildingFloors(2);
        $house->setBuildingHeatedFloors([0, 1, 2, 3]);
        $house->setBuildingRoof('steep');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(4, $fs->getTotalFloorsNumber());
        $this->assertEquals(4, $fs->getHeatedFloorsNumber());
        $this->assertEquals(true, $fs->isBasementHeated());
        $this->assertEquals(true, $fs->isAtticHeated());
        $this->assertEquals(true, $fs->isGroundFloorHeated());
        $this->assertEquals(false, $fs->hasUnheatedFloors());
        $this->assertEquals('Dach', $fs->getTopLabel());
        $this->assertEquals('Izolacja dachu', $fs->getTopIsolationLabel());
        $this->assertEquals('Piwnica', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi piwnicy', $fs->getBottomIsolationLabel());
    }


    public function testThreeFloorsWithBasementFlatRoofOneHeated()
    {
        $house = new House();
        $house->setBuildingFloors(3);
        $house->setBuildingHeatedFloors([2]);
        $house->setBuildingRoof('flat');
        $house->setHasBasement(true);

        $instance = $this->makeInstance($house);
        $fs = new FloorsService($instance);

        $this->assertEquals(4, $fs->getTotalFloorsNumber());
        $this->assertEquals(1, $fs->getHeatedFloorsNumber());
        $this->assertEquals(false, $fs->isBasementHeated());
        $this->assertEquals(false, $fs->isAtticHeated());
        $this->assertEquals(false, $fs->isGroundFloorHeated());
        $this->assertEquals(true, $fs->hasUnheatedFloors());
        $this->assertEquals('Strop', $fs->getTopLabel());
        $this->assertEquals('Izolacja stropu', $fs->getTopIsolationLabel());
        $this->assertEquals('Podłoga', $fs->getBottomLabel());
        $this->assertEquals('Izolacja podłogi', $fs->getBottomIsolationLabel());
    }

    protected function mockSession()
    {
        $mock = Mockery::mock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        return $mock;
    }

    protected function mockEM()
    {
        $mock = Mockery::mock('Doctrine\ORM\EntityManager');

        return $mock;
    }
}
