<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\DimensionsService;
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
        $house->setBuildingFloors(3);
        $house->setBuildingRoof('flat');
        $house->setBuildingHeatedFloors([1, 2]);
        $house->setVentilationType('natural');
        $house->setPrimaryWallMaterial($m);
        $house->setWallSize(50);
        $house->setNumberWindows(10);

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);

        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        return $instance;
    }

    public function testWindowsArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $d = new DimensionsService($this->makeInstance(), $f);

        $this->assertEquals(25, $d->getWindowsArea());
    }

    public function testTotalWallArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $f->shouldReceive('isBasementHeated')->andReturn(false);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $this->assertEquals(236, $d->getTotalWallArea());
    }

    public function testTotalWallAreaWithBasement()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $f->shouldReceive('isBasementHeated')->andReturn(true);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(3);
        $instance->get()->getHouse()->setBuildingRoof('flat');
        $instance->get()->getHouse()->setBuildingHeatedFloors([0, 1, 2, 3]);

        $this->assertEquals(354, $d->getTotalWallArea());
    }

    public function testTotalWallAreaWithSteepRoof()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $f->shouldReceive('isBasementHeated')->andReturn(false);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(3);
        $instance->get()->getHouse()->setBuildingRoof('steep');
        $instance->get()->getHouse()->setBuildingHeatedFloors([1, 2, 3]);

        $this->assertEquals(270.8, $d->getTotalWallArea());
    }

    public function testNumberOfHeatedFloors()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(3);
        $instance->get()->getHouse()->setBuildingRoof('flat');
        $instance->get()->getHouse()->setBuildingHeatedFloors([1, 2, 3]);

        $this->assertEquals(3, $d->getNumberOfHeatedFloors());
    }

    public function testExternalBuildingLengthRegularCaseWithArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setArea(144);

        $this->assertEquals(12, $d->getExternalBuildingLength());
        $this->assertEquals(12, $d->getExternalBuildingWidth());
    }

    public function testExternalBuildingLengthRegularCaseWithDimensions()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingLength(15);
        $instance->get()->getHouse()->setBuildingWidth(10);

        $this->assertEquals(15, $d->getExternalBuildingLength());
        $this->assertEquals(10, $d->getExternalBuildingWidth());
    }

    public function testExternalBuildingLengthIrregularCase()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingShape('irregular');
        $instance->get()->getHouse()->setBuildingContourFreeArea(9);
        $instance->get()->getHouse()->setBuildingLength(15);
        $instance->get()->getHouse()->setBuildingWidth(8);

        $this->assertEquals(18, $d->getExternalBuildingLength());
        $this->assertEquals(11, $d->getExternalBuildingWidth());
    }

    public function testInternalBuildingLength()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingShape('irregular');
        $instance->get()->getHouse()->setBuildingContourFreeArea(9);
        $instance->get()->getHouse()->setBuildingLength(15);
        $instance->get()->getHouse()->setBuildingWidth(8);

        $this->assertEquals(17, $d->getInternalBuildingLength());
        $this->assertEquals(10, $d->getInternalBuildingWidth());
    }

    public function testFloorArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setArea(100);

        $this->assertEquals(100, $d->getFloorArea());
    }

    public function testFloorAreaWidthDimensions()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setArea(0);
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);

        $this->assertEquals(81, $d->getFloorArea());
    }
/*
    public function testTotalFloorsNumber()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $this->assertEquals(1, $d->getTotalFloorsNumber());
    }

    public function testTotalFloorsNumberWithSteepRoof()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingRoof('steep');

        $this->assertEquals(2, $d->getTotalFloorsNumber());
    }

    public function testHeatedFloorsNumber()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1]);
        $instance->get()->getHouse()->setBuildingRoof('flat');

        $this->assertEquals(1, $d->getHeatedFloorsNumber());
    }

    public function testHeatedFloorsNumberWithSteepRoof()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1, 2]);
        $instance->get()->getHouse()->setBuildingRoof('steep');

        $this->assertEquals(2, $d->getHeatedFloorsNumber());
    }
*/
    public function testTotalHouseArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('getTotalFloorsNumber')->andReturn(1);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1]);
        $instance->get()->getHouse()->setBuildingRoof('flat');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);

        $this->assertEquals(81, $d->getTotalHouseArea());
    }

    public function testTotalHouseAreaWithSteepRoof()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('getTotalFloorsNumber')->andReturn(2);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1, 2]);
        $instance->get()->getHouse()->setBuildingRoof('steep');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);

        $this->assertEquals(137.7, $d->getTotalHouseArea());
    }

    public function testHeatedHouseArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('getHeatedFloorsNumber')->andReturn(1);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1]);
        $instance->get()->getHouse()->setBuildingRoof('flat');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);

        $this->assertEquals(81, $d->getHeatedHouseArea());
    }

    public function testHeatedHouseAreaWithSteepRoofAndGarage()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $f->shouldReceive('isAtticHeated')->andReturn(true);
        $f->shouldReceive('isGroundFloorHeated')->andReturn(true);
        $f->shouldReceive('getHeatedFloorsNumber')->andReturn(2);

        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1, 2]);
        $instance->get()->getHouse()->setBuildingRoof('steep');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);
        $instance->get()->getHouse()->setHasGarage(true);

        $this->assertEquals(117.7, $d->getHeatedHouseArea());
    }

    public function testDoorsArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setFloorHeight(3);
        $instance->get()->getHouse()->setNumberDoors(2);

        $this->assertEquals(4.8, $d->getDoorsArea());
    }

    public function testHouseCubature()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingFloors(1);
        $instance->get()->getHouse()->setBuildingHeatedFloors([1, 2]);
        $instance->get()->getHouse()->setBuildingRoof('steep');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);
        $instance->get()->getHouse()->setHasGarage(true);

        $this->assertEquals(336.96, $d->getHouseCubature());
    }

    public function testRoofArea()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingRoof('flat');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);

        $this->assertEquals(100, $d->getRoofArea());
    }

    public function testRoofAreaWithSteepRoof()
    {
        $f = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $instance = $this->makeInstance();
        $d = new DimensionsService($instance, $f);

        $instance->get()->getHouse()->setBuildingRoof('steep');
        $instance->get()->getHouse()->setWallSize(50);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setBuildingLength(10);

        $this->assertEquals(112.71, $d->getRoofArea());
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
