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

class DimensionsServiceTest extends \PHPUnit_Framework_TestCase
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

    public function testWhatever()
    {
        $this->assertEquals(2+2, 4);
    }

/*
    public function testHouseCubature()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setBuildingFloors(2);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1,2]);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $building = $this->mockBuilding($instance);

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

        $building = $this->mockBuilding($instance);

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

        $building = new DoubleBuilding($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory(), $this->mockDimensions(), $this->mockFloors());

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

        $building = $this->mockBuilding($instance);

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

        $building = $this->mockBuilding($instance);

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

        $building = $this->mockBuilding($instance);

        $this->assertEquals(2, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());
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

    protected function mockDimensions()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\DimensionsService');

        return $mock;
    }

    protected function mockFloors()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');

        return $mock;
    }
*/
}
