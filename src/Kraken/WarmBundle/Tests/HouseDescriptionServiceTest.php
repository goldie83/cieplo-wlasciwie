<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\HouseDescriptionService;
use Kraken\WarmBundle\Service\InstanceService;
use Mockery;

class HouseDescriptionServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testHeadline()
    {
        $house = new House();
        $house->setBuildingFloors(4);
        $house->setBuildingLength(10);
        $house->setBuildingWidth(12);

        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $calc->setConstructionYear(2002);
        $calc->setIndoorTemperature(20);
        $calc->setLatitude(51.11);
        $calc->setLongitude(17.01);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $desc = new HouseDescriptionService($instance, $this->mockDimensions(), $this->mockFloors());

        $this->assertEquals('Budynek jednorodzinny trzypiętrowy', $desc->getHeadline());
    }

    public function testAreaDetails()
    {
        $house = new House();
        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $dimensions = Mockery::mock('Kraken\WarmBundle\Service\DimensionsService');
        $dimensions->shouldReceive('getHeatedHouseArea')->andReturn(140);
        $dimensions->shouldReceive('getTotalHouseArea')->andReturn(210);

        $desc = new HouseDescriptionService($instance, $dimensions, $this->mockFloors());

        $this->assertEquals('ogrzewana: 140m<sup>2</sup>, całkowita: 210m<sup>2</sup>', $desc->getAreaDetails());
    }

    public function testHeatedFloorsDetails()
    {
        $house = new House();
        $house->setNumberFloors(3);
        $house->setBuildingLength(10);
        $house->setBuildingWidth(12);

        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $calc->setConstructionYear(2002);
        $calc->setIndoorTemperature(20);
        $calc->setLatitude(51.11);
        $calc->setLongitude(17.01);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $floors = [
            ['label' => 'Piwnica', 'heated' => false],
            ['label' => 'Parter', 'heated' => true],
            ['label' => 'Piętro', 'heated' => true],
            ['label' => 'Poddasze', 'heated' => false],
        ];

        $dimensions = Mockery::mock('Kraken\WarmBundle\Service\DimensionsService');
        $dimensions->shouldReceive('getTotalFloorsNumber')->andReturn(3);

        $floors = Mockery::mock('Kraken\WarmBundle\Service\FloorsService');
        $floors->shouldReceive('isGroundFloorHeated')->andReturn(true);

        $desc = new HouseDescriptionService($instance, $dimensions, $floors);

        $this->assertEquals('parter, 1. piętro', $desc->getHeatedFloorsDetails());
    }

    public function testWallDetails()
    {
        $house = new House();
        $house->setConstructionType('canadian');
        $house->setBuildingFloors(3);
        $house->setBuildingLength(10);
        $house->setBuildingWidth(12);

        $m1 = new Material();
        $m1->setName('cegła pełna');
        $m2 = new Material();
        $m2->setName('pustak żużlobetonowy');
        $m3 = new Material();
        $m3->setName('styropian');

        #1
        $l1 = new Layer();
        $l1->setMaterial($m3);
        $l1->setSize(15);
        $house->setExternalIsolationLayer($l1);
        $house->setWallSize(30);

        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $calc->setConstructionYear(2002);
        $calc->setIndoorTemperature(20);
        $calc->setLatitude(51.11);
        $calc->setLongitude(17.01);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $dimensions = Mockery::mock('Kraken\WarmBundle\Service\DimensionsService');
        $dimensions->shouldReceive('getTotalFloorsNumber')->andReturn(2);
        $desc = new HouseDescriptionService($instance, $dimensions, $this->mockFloors());

        $this->assertEquals('30cm, konstrukcja: szkielet drewniany (dom kanadyjski), izolacja: styropian 15cm', $desc->getWallDetails());

        #2
        $l3 = new Layer();
        $l3->setMaterial($m3);
        $l3->setSize(15);

        $house->setConstructionType('traditional');
        $house->setPrimaryWallMaterial($m1);
        $house->setSecondaryWallMaterial($m2);
        $house->setExternalIsolationLayer($l3);

        $this->assertEquals('30cm, konstrukcja: cegła pełna + pustak żużlobetonowy, izolacja: styropian 15cm', $desc->getWallDetails());
    }

    public function testGroundDetails()
    {
        $house = new House();
        $house->setBuildingFloors(3);
        $house->setBuildingLength(10);
        $house->setBuildingWidth(12);

        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        #1
        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('isBasementHeated')->andReturn(true);

        $desc = new HouseDescriptionService($instance, $this->mockDimensions(), $this->mockFloors());

        $this->assertEquals('bez izolacji', $desc->getGroundDetails());

        #2
        $m1 = new Material();
        $m1->setName('styropian');
        $l1 = new Layer();
        $l1->setMaterial($m1);
        $l1->setSize(15);
        $house->setBottomIsolationLayer($l1);
        $this->assertEquals('styropian 15cm', $desc->getGroundDetails());

        #3
        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('isBasementHeated')->andReturn(false);
        $building->shouldReceive('isGroundFloorHeated')->andReturn(true);

        $desc = new HouseDescriptionService($instance, $this->mockDimensions(), $this->mockFloors());

        $house->setBottomIsolationLayer($l1);
        $this->assertEquals('styropian 15cm', $desc->getGroundDetails());

        #4
        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('isBasementHeated')->andReturn(false);
        $building->shouldReceive('isGroundFloorHeated')->andReturn(false);

        $desc = new HouseDescriptionService($instance, $this->mockDimensions(), $this->mockFloors());

        $house->setBottomIsolationLayer($l1);
        $this->assertEquals('styropian 15cm', $desc->getGroundDetails());
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
}
