<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;
use Kraken\WarmBundle\Entity\FuelConsumption;

class FuelServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testFuelEnergy()
    {
        $fs = new FuelService();

        $f1 = new Fuel;
        $f1->setType(Fuel::TYPE_COAL);
        $f1->setEnergy(28);
        $this->assertEquals(15512, $fs->getFuelEnergy($f1, 2));

        $f2 = new Fuel;
        $f2->setType(Fuel::TYPE_SAND_COAL);
        $f2->setEnergy(22);
        $this->assertEquals(30470, $fs->getFuelEnergy($f2, 5));

        $f3 = new Fuel;
        $f3->setType(Fuel::TYPE_BROWN_COAL);
        $f3->setEnergy(18);
        $this->assertEquals(9973, $fs->getFuelEnergy($f3, 2));

        $f4 = new Fuel;
        $f4->setType(Fuel::TYPE_PELLET);
        $f4->setEnergy(19);
        $this->assertEquals(63157, $fs->getFuelEnergy($f4, 12));

        $f5 = new Fuel;
        $f5->setType(Fuel::TYPE_NATURAL_GAS);
        $f5->setEnergy(3.6);
        $this->assertEquals(2, $fs->getFuelEnergy($f5, 2));

        $f8 = new Fuel;
        $f8->setType(Fuel::TYPE_WOOD);
        $f8->setEnergy(19);
        $this->assertEquals(24631, $fs->getFuelEnergy($f8, 10));

        $f9 = new Fuel;
        $f9->setType(Fuel::TYPE_ELECTRICITY);
        $f9->setEnergy(3.6);
        $this->assertEquals(3, $fs->getFuelEnergy($f9, 3));
    }

    public function testFormatFuelConsumption()
    {
        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('sand_coal');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(2);
        $c->addFuelConsumption($fc);
        $this->assertEquals('2t miału', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType(Fuel::TYPE_COAL);
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(2.2);
        $c->addFuelConsumption($fc);
        $this->assertEquals('2,2t węgla kamiennego', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('wood');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(1.5);
        $c->addFuelConsumption($fc);
        $this->assertEquals('1,5mp drewna', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('natural_gas');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(20);
        $c->addFuelConsumption($fc);
        $this->assertEquals('20kWh gazu ziemnego', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('coke');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(3.3);
        $c->addFuelConsumption($fc);
        $this->assertEquals('3,3t koksu', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('pellet');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(4);
        $c->addFuelConsumption($fc);
        $this->assertEquals('4t pelletu', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('electricity');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(1567);
        $c->addFuelConsumption($fc);
        $this->assertEquals('1567kWh prądu', $fs->formatFuelConsumption($c));

        $fs = new FuelService();
        $c = new Calculation();
        $f1 = new Fuel;
        $f1->setType('brown_coal');
        $fc = new FuelConsumption;
        $fc->setFuel($f1);
        $fc->setConsumption(7.5);
        $c->addFuelConsumption($fc);

        $f2 = new Fuel;
        $f2->setType('coke');
        $fc2 = new FuelConsumption;
        $fc2->setFuel($f2);
        $fc2->setConsumption(1);
        $c->addFuelConsumption($fc2);

        $this->assertEquals('7,5t węgla brunatnego, 1t koksu', $fs->formatFuelConsumption($c));
    }
}
