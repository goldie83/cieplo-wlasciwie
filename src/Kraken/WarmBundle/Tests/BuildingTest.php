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
        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

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

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(24.9, $building->getEnergyLossToUnderground());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setBottomIsolationLayer($isolation);

        $this->assertEquals(14.1, $building->getEnergyLossToUnderground());

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
        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(0, $building->getFloorEnergyLossToUnheated());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setBottomIsolationLayer($isolation);

        $this->assertEquals(0, $building->getFloorEnergyLossToUnheated());

        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);

        $this->assertEquals(7.61, $building->getFloorEnergyLossToUnheated());
    }

    public function testHouseCubature()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(round(9 * 9 * 2 * 2.6, 2), $building->getHouseCubature());

        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);
        $instance->get()->getHouse()->setBuildingRoof('oblique');

        $this->assertEquals(round(9 * 9 * 2.6 * 1.6, 2), $building->getHouseCubature());
    }

    public function testWallArea()
    {
        $instance = $this->makeInstance();
        $instance->get()->setBuildingType('single_house');
        $instance->get()->getHouse()->setBuildingLength(10);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1]);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(4, $building->getNumberOfWalls());
        $this->assertEquals(2.6, $building->getHouseHeight());
        $this->assertEquals(104, $building->getWallArea($instance->get()->getHouse()->getWalls()->first()));

        $instance = $this->makeInstance();
        $instance->get()->setBuildingType('double_house');
        $instance->get()->getHouse()->setBuildingLength(10);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $building = new DoubleBuilding($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(3, $building->getNumberOfWalls());
        $this->assertEquals(5.55, $building->getHouseHeight());
        $this->assertEquals(166.5, $building->getWallArea($instance->get()->getHouse()->getWalls()->first()));
    }

    public function testNumberOfHeatedFloors()
    {
        //jednopiętrowy, dach skosny - ogrzewany tylko parter
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1]);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setBuildingRoof('steep');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(1, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // parterówka, płaski dach, - ogrzewany tylko parter
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1]);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(1, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertTrue($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // 4 piętra, piwnica i skośny dach - ogrzewany parter i piętro
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setBuildingFloors(3);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setBuildingRoof('steep');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(2, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());
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

        $building = new Building($instance, $this->mockVentilation(), new WallService($instance), $this->mockWallFactory());

        $this->assertEquals(240.8, $building->getExternalWallEnergyLossFactor());

        $instance->get()->getHouse()->setExternalIsolationLayer($l2);

        $this->assertEquals(52.0128, $building->getExternalWallEnergyLossFactor());
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
        $mock->shouldReceive('getThermalConductance')->andReturn(0.25);

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
}
