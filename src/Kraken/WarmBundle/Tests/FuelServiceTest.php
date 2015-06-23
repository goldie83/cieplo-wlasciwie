<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;

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

    public function testFormatFuelAmount()
    {
        $fs = new FuelService();
        $c = new Calculation();

        $c->setFuelType('sand_coal');
        $this->assertEquals('2t miału', $fs->formatFuelAmount(2, $c));

        $c->setFuelType('coal');
        $this->assertEquals('2,2t węgla kamiennego', $fs->formatFuelAmount(2.2, $c));

        $c->setFuelType('wood');
        $this->assertEquals('1,5mp drewna', $fs->formatFuelAmount(1.5, $c));

        $c->setFuelType('gas_e');
        $this->assertEquals('20m<sup>3</sup> gazu ziemnego', $fs->formatFuelAmount(20, $c));

        $c->setFuelType('coke');
        $this->assertEquals('3,3t koksu', $fs->formatFuelAmount(3.3, $c));

        $c->setFuelType('pellet');
        $this->assertEquals('4t pelletu', $fs->formatFuelAmount(4, $c));

        $c->setFuelType('electricity');
        $this->assertEquals('1567kWh prądu', $fs->formatFuelAmount(1567, $c));

        $c->setFuelType('brown_coal');
        $this->assertEquals('7,5t węgla brunatnego', $fs->formatFuelAmount(7.5, $c));
    }
}
