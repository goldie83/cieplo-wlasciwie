<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Calculator\EnergyPricing;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;
use Kraken\WarmBundle\Entity\HeatingDevice;
use Kraken\WarmBundle\Entity\HeatingVariant;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Entity\Wall;
use Kraken\WarmBundle\Service\HouseDescriptionService;
use Kraken\WarmBundle\Service\InstanceService;
use Mockery;

class HouseDescriptionServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testHeadline()
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

        $desc = new HouseDescriptionService($instance, Mockery::mock('Kraken\WarmBundle\Service\Building'));

        $this->assertEquals('Budynek jednorodzinny trzypiętrowy A.D. 2002 (10m x 12m w obrysie zewn.)', $desc->getHeadline());
    }

    public function testAreaDetails()
    {
        $house = new House();
        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('getHeatedHouseArea')->andReturn(140);
        $building->shouldReceive('getTotalHouseArea')->andReturn(210);

        $desc = new HouseDescriptionService($instance, $building);

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
        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('getFloors')->andReturn($floors);

        $desc = new HouseDescriptionService($instance, $building);

        $this->assertEquals('parter, piętro', $desc->getHeatedFloorsDetails());
    }

    public function testWallDetails()
    {
        $house = new House();
        $house->setConstructionType('canadian');
        $house->setNumberFloors(3);
        $house->setBuildingLength(10);
        $house->setBuildingWidth(12);

        $m1 = new Material;
        $m1->setName('cegła pełna');
        $m2 = new Material;
        $m2->setName('pustak żużlobetonowy');
        $m3 = new Material;
        $m3->setName('styropian');

        #1
        $l1 = new Layer;
        $l1->setMaterial($m3);
        $l1->setSize(15);
        $w1 = new Wall;
        $w1->setExtraIsolationLayer($l1);
        $house->addWall($w1);

        $calc = new Calculation();
        $calc->setBuildingType('single_house');
        $calc->setHouse($house);
        $calc->setConstructionYear(2002);
        $calc->setIndoorTemperature(20);
        $calc->setLatitude(51.11);
        $calc->setLongitude(17.01);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $desc = new HouseDescriptionService($instance, Mockery::mock('Kraken\WarmBundle\Service\Building'));

        $this->assertEquals('15cm, w tym szkielet drewniany (dom kanadyjski) + styropian 15cm', $desc->getWallDetails());
        
        #2
        $house->removeWall($w1);
        $house->setConstructionType('traditional');

        $l1 = new Layer;
        $l1->setMaterial($m1);
        $l1->setSize(25);
        $l2 = new Layer;
        $l2->setMaterial($m2);
        $l2->setSize(15);
        $l3 = new Layer;
        $l3->setMaterial($m3);
        $l3->setSize(15);
        $w1 = new Wall;
        $w1->setConstructionLayer($l1);
        $w1->setOutsideLayer($l2);
        $w1->setExtraIsolationLayer($l3);
        $house->addWall($w1);

        $this->assertEquals('55cm, w tym cegła pełna 25cm + pustak żużlobetonowy 15cm + styropian 15cm', $desc->getWallDetails());
    }

    public function testGroundDetails()
    {
        $house = new House();
        $house->setNumberFloors(3);
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

        $desc = new HouseDescriptionService($instance, $building);

        $this->assertEquals('podgłoga w piwnicy bez izolacji', $desc->getGroundDetails());

        #2
        $m1 = new Material;
        $m1->setName('styropian');
        $l1 = new Layer;
        $l1->setMaterial($m1);
        $l1->setSize(15);
        $house->setBasementFloorIsolationLayer($l1);
        $this->assertEquals('izolacja podłogi w piwnicy: styropian 15cm', $desc->getGroundDetails());

        #3
        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('isBasementHeated')->andReturn(false);
        $building->shouldReceive('isGroundFloorHeated')->andReturn(true);

        $desc = new HouseDescriptionService($instance, $building);

        $house->setGroundFloorIsolationLayer($l1);
        $this->assertEquals('izolacja podłogi na gruncie: styropian 15cm', $desc->getGroundDetails());

        #4
        $building = Mockery::mock('Kraken\WarmBundle\Service\Building');
        $building->shouldReceive('isBasementHeated')->andReturn(false);
        $building->shouldReceive('isGroundFloorHeated')->andReturn(false);

        $desc = new HouseDescriptionService($instance, $building);

        $house->setLowestCeilingIsolationLayer($l1);
        $this->assertEquals('izolacja stropu nad nieogrzewanym parterem: styropian 15cm', $desc->getGroundDetails());
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
