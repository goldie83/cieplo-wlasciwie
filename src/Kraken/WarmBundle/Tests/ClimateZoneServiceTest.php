<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Calculator\ClimateZoneService;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Service\InstanceService;
use Mockery;

class ClimateZoneServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testClimateZone()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $calc->setLatitude(51.11);
        $calc->setLongitude(17.01);
        $instance = new InstanceService($this->mockSession(), $this->mockEM());
        $instance->setCustomCalculation($calc);

        $c = new ClimateZoneService($instance);

        // Wroclaw
        $this->assertEquals(2, $c->getClimateZone());

        // Szczecin
        $calc->setLatitude(53.44);
        $calc->setLongitude(14.41);
        $this->assertEquals(1, $c->getClimateZone());

        // Koszalin
        $calc->setLatitude(54.1945);
        $calc->setLongitude(16.17);
        $this->assertEquals(1, $c->getClimateZone());

        // Elbląg
        $calc->setLatitude(54.15);
        $calc->setLongitude(19.40);
        $this->assertEquals(2, $c->getClimateZone());

        // Brodnica
        $calc->setLatitude(53.25);
        $calc->setLongitude(19.38);
        $this->assertEquals(3, $c->getClimateZone());

        // Augustów
        $calc->setLatitude(53.84);
        $calc->setLongitude(22.98);
        $this->assertEquals(5, $c->getClimateZone());

        // Łomża
        $calc->setLatitude(53.17);
        $calc->setLongitude(22.07);
        $this->assertEquals(4, $c->getClimateZone());

        // Poznań
        $calc->setLatitude(52.40);
        $calc->setLongitude(16.94);
        $this->assertEquals(2, $c->getClimateZone());

        // Opole
        $calc->setLatitude(50.67);
        $calc->setLongitude(17.91);
        $this->assertEquals(3, $c->getClimateZone());

        // Nowy Sącz
        $calc->setLatitude(49.62);
        $calc->setLongitude(20.71);
        $this->assertEquals(3, $c->getClimateZone());

        // Zakopane
        $calc->setLatitude(49.29);
        $calc->setLongitude(19.95);
        $this->assertEquals(5, $c->getClimateZone());

        // Sejny
        $calc->setLatitude(54.107723);
        $calc->setLongitude(23.340454);
        $this->assertEquals(5, $c->getClimateZone());

        // Krynica
        $calc->setLatitude(49.414547);
        $calc->setLongitude(20.961914);
        $this->assertEquals(4, $c->getClimateZone());

        // Karpacz
        $calc->setLatitude(50.771208);
        $calc->setLongitude(15.754395);
        $this->assertEquals(3, $c->getClimateZone());

        // Zgorzelec
        $calc->setLatitude(51.14);
        $calc->setLongitude(15.00);
        $this->assertEquals(3, $c->getClimateZone());
    }

    protected function mockHeating()
    {
        $heating = Mockery::mock('Kraken\WarmBundle\Calculator\HeatingSeason');
        $heating->shouldReceive('getAverageTemperature')->andReturn(1);

        return $heating;
    }

    protected function mockFuel()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\FuelService');

        return $mock;
    }

    protected function mockBuilding()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Calculator\BuildingInterface');

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
