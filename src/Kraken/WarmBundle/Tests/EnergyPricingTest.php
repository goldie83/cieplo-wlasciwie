<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Calculator\EnergyPricing;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;
use Kraken\WarmBundle\Entity\HeatingDevice;
use Kraken\WarmBundle\Entity\HeatingVariant;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Service\InstanceService;
use Mockery;

class EnergyPricingTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectSetupCosts()
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

        $ep = new EnergyPricing($instance, $this->mockEnergyCalculator(), $this->mockEM(), $this->mockHeatingSeason());

        $manualStove = new HeatingDevice;
        $manualStove->setType(HeatingDevice::TYPE_MANUAL_STOVE);
        $coke = new Fuel;
        $coke->setType(Fuel::TYPE_COKE);

        $hv = new HeatingVariant();
        $hv->setHeatingDevice($manualStove);
        $hv->setFuel($coke);

        // #1
        $costs = $ep->collectSetupCosts($hv);
        $expectedCosts = [['Komin', 10000], ['Kotłownia', 12000], ['Kocioł zasypowy', 3500]];
        $this->assertEquals($expectedCosts, $costs);

        // #2
        $actualHeatingDevice = new HeatingDevice;
        $actualHeatingDevice->setType(HeatingDevice::TYPE_MANUAL_STOVE);
        $calc->setHeatingDevice($actualHeatingDevice);

        $costs = $ep->collectSetupCosts($hv);
        $expectedCosts = [];
        $this->assertEquals($expectedCosts, $costs);

        // #3
        $actualHeatingDevice = new HeatingDevice;
        $actualHeatingDevice->setType(HeatingDevice::TYPE_MASONRY_STOVE);
        $calc->setHeatingDevice($actualHeatingDevice);

        $costs = $ep->collectSetupCosts($hv);
        $expectedCosts = [['Kotłownia', 12000], ['Kocioł zasypowy', 3500]];
        $this->assertEquals($expectedCosts, $costs);

        // #4
        $actualHeatingDevice = new HeatingDevice;
        $actualHeatingDevice->setType(HeatingDevice::TYPE_PELLET_STOVE);
        $calc->setHeatingDevice($actualHeatingDevice);

        $costs = $ep->collectSetupCosts($hv);
        $expectedCosts = [['Kocioł zasypowy', 3500]];
        $this->assertEquals($expectedCosts, $costs);

        // #5
        $actualHeatingDevice = new HeatingDevice;
        $actualHeatingDevice->setType(HeatingDevice::TYPE_GAS_STOVE_OLD);
        $calc->setHeatingDevice($actualHeatingDevice);

        $costs = $ep->collectSetupCosts($hv);
        $expectedCosts = [['Komin', 10000], ['Kotłownia', 12000], ['Kocioł zasypowy', 3500]];
        $this->assertEquals($expectedCosts, $costs);

        // #6
        $gasStoveCondensing = new HeatingDevice;
        $gasStoveCondensing->setType(HeatingDevice::TYPE_GAS_STOVE_CONDENSING);
        $naturalGas = new Fuel;
        $naturalGas->setType(Fuel::TYPE_NATURAL_GAS);

        $hv2 = new HeatingVariant();
        $hv2->setHeatingDevice($gasStoveCondensing);
        $hv2->setFuel($naturalGas);

        $actualHeatingDevice = new HeatingDevice;
        $actualHeatingDevice->setType(HeatingDevice::TYPE_MANUAL_STOVE);
        $calc->setHeatingDevice($actualHeatingDevice);

        $costs = $ep->collectSetupCosts($hv2);
        $expectedCosts = [['Kocioł gazowy', 5000], ['Przyłącze gazowe', 8000]];
        $this->assertEquals($expectedCosts, $costs);
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

    protected function mockEnergyCalculator()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Calculator\EnergyCalculator');

        return $mock;
    }

    protected function mockHeatingSeason()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Calculator\HeatingSeason');

        return $mock;
    }
}
