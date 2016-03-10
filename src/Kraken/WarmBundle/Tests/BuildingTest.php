<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Wall;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\Building;
use Kraken\WarmBundle\Service\DoubleBuilding;
use Kraken\WarmBundle\Service\WallService;
use Mockery;

class BuildingTest extends \PHPUnit_Framework_TestCase
{
    protected function makeInstance()
    {
        $m = new Material();
        $m->setName('stuff');
        $m->setLambda(0.2);

        $house = new House();
        $house->setBuildingWidth(10);
        $house->setBuildingLength(10);
        $house->setVentilationType('natural');
        $house->setPrimaryWallMaterial($m);
        $house->setWallSize(50);

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);

        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        return $instance;
    }

    public function testEnergyLossThroughGroundFloor()
    {
        $instance = $this->makeInstance();
        $building = $this->mockBuilding($instance);

        $this->assertEquals(28.68, $building->getEnergyLossThroughGroundFloor());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setBottomIsolationLayer($isolation);

        $this->assertEquals(7.8, $building->getEnergyLossThroughGroundFloor());
    }

    public function testEnergyLossToUnderground()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([0,1,2]);

        $building = $this->mockBuilding($instance);

        $this->assertEquals(24.9, $building->getEnergyLossToUnderground());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setBottomIsolationLayer($isolation);

        $this->assertEquals(6.21, $building->getEnergyLossToUnderground());

        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $f->shouldReceive('isBasementHeated')->andReturn(false);

        $building = $this->mockBuilding($instance, null, $f);

        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);

        $this->assertEquals(0, $building->getEnergyLossToUnderground());
    }

    public function testFloorEnergyLossToUnheated()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([0,1,2]);

        $building = $this->mockBuilding($instance);

        $this->assertEquals(0, $building->getFloorEnergyLossToUnheated());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setBottomIsolationLayer($isolation);

        $this->assertEquals(0, $building->getFloorEnergyLossToUnheated());


        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $f->shouldReceive('isBasementHeated')->andReturn(false);

        $building = $this->mockBuilding($instance, null, $f);

        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);

        $this->assertEquals(9.40, $building->getFloorEnergyLossToUnheated());
    }

    public function testExternalWallEnergyLossFactor()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);
        $instance->get()->getHouse()->setNumberDoors(2);
        $instance->get()->getHouse()->setNumberWindows(10);
        $instance->get()->getHouse()->setBuildingLength(9.5);
        $instance->get()->getHouse()->setBuildingWidth(10.5);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setHasBalcony(false);
        $instance->get()->getHouse()->setBuildingRoof('flat');
        $instance->get()->getHouse()->setDoorsType('old_wooden');
        $instance->get()->getHouse()->setWindowsType('new_double_glass');

        $m1 = new Material();
        $m1->setLambda(0.65);
        $m1->setName('pustak żużlobetonowy');

        $m2 = new Material();
        $m2->setName('styropian');
        $m2->setLambda(0.038);

        $instance->get()->getHouse()->setWallSize(52);
        $instance->get()->getHouse()->setPrimaryWallMaterial($m1);

        $l2 = new Layer();
        $l2->setMaterial($m2);
        $l2->setSize(12);

        $building = $this->mockBuilding($instance);

        $this->assertEquals(300, $building->getExternalWallEnergyLossFactor());

        $instance->get()->getHouse()->setExternalIsolationLayer($l2);

        $this->assertEquals(48, $building->getExternalWallEnergyLossFactor());
    }

    protected function mockVentilation()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\VentilationService');

        return $mock;
    }

    protected function mockWall()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\WallService');
        $mock->shouldReceive('getSize')->andReturn(0.4);
        $mock->shouldReceive('getThermalConductance')->andReturn(0.25, 0.04);

        return $mock;
    }

    protected function mockWallFactory()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\WallFactory');

        return $mock;
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

    protected function mockDimensions()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\DimensionsService');
        $mock->shouldReceive('getExternalBuildingLength')->andReturn(10);
        $mock->shouldReceive('getExternalBuildingWidth')->andReturn(10);
        $mock->shouldReceive('getTotalWallArea')->andReturn(1200);
        $mock->shouldReceive('getBasementHeight')->andReturn(0.9*2.6);

        return $mock;
    }

    protected function mockFloors()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $mock->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $mock->shouldReceive('isBasementHeated')->andReturn(true);

        return $mock;
    }

    protected function mockBuilding($instance, $dimensions = null, $floors = null)
    {
        if (!$dimensions) {
            $dimensions = $this->mockDimensions();
        }

        if (!$floors) {
            $floors = $this->mockFloors();
        }

        return new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory(), $dimensions, $floors);
    }
}
