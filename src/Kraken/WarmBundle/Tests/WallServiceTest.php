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
    public function testThermalConductance()
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

    public function testThermalConductanceWithAirGapIsolation()
    {
        $house = new House();
        $calc = new Calculation();
        $calc->setHouse($house);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material();
        $m1->setName('Pustka powietrzna');

        $l1 = new Layer();
        $l1->setSize(5);
        $l1->setMaterial($m1);

        $m2 = new Material();
        $m2->setLambda(0.56);

        $house->setWallSize(45);
        $house->setPrimaryWallMaterial($m2);

        $this->assertEquals(1.24, $service->getThermalConductance());

        $house->setInternalIsolationLayer($l1);

        $this->assertEquals(1.12, $service->getThermalConductance());
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
