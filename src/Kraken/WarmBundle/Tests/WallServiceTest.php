<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\WallService;
use Mockery;

class WallServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testThermalConductanceWithPrimaryWallMaterial()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material();
        $m1->setLambda(0.56);
        $m2 = new Material();
        $m2->setLambda(0.04);

        $house->setWallSize(52);
        $house->setPrimaryWallMaterial($m1);

        $this->assertEquals(1.08, $service->getThermalConductance());

        $l2 = new Layer();
        $l2->setSize(12);
        $l2->setMaterial($m2);
        $house->setExternalIsolationLayer($l2);

        $this->assertEquals(0.27, $service->getThermalConductance());
    }

    public function testThermalConductanceWithSecondaryWallMaterial()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material();
        $m1->setLambda(0.55);
        $m2 = new Material();
        $m2->setLambda(0.25);

        $house->setWallSize(50);
        $house->setPrimaryWallMaterial($m1);
        $house->setSecondaryWallMaterial($m2);

        $this->assertEquals(0.77, $service->getThermalConductance());
    }

    public function testThermalConductanceWithFullFeaturedWall()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material();
        $m1->setLambda(0.55);
        $m2 = new Material();
        $m2->setLambda(0.25);

        $l = new Layer();
        $airGap = new Material();
        $airGap->setName('pustka powietrzna');
        $l->setMaterial($airGap);
        $l->setSize(5);

        $l2 = new Layer();
        $styro = new Material();
        $styro->setLambda(0.04);
        $l2->setMaterial($styro);
        $l2->setSize(15);

        $house->setWallSize(50);
        $house->setPrimaryWallMaterial($m1);
        $house->setSecondaryWallMaterial($m2);
        $house->setInternalIsolationLayer($l);
        $house->setExternalIsolationLayer($l2);

        $this->assertEquals(0.21, $service->getThermalConductance());
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
