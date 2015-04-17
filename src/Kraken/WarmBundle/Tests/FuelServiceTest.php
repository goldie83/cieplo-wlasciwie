<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Entity\Calculation;

class FuelServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testFuelEnergy()
    {
        $fs = new FuelService();

        $this->assertEquals(15540, $fs->getFuelEnergy('coal', 2));
        $this->assertEquals(33300, $fs->getFuelEnergy('sand_coal', 5));
        $this->assertEquals(10000, $fs->getFuelEnergy('brown_coal', 2));
        $this->assertEquals(60000, $fs->getFuelEnergy('pellet', 12));
        $this->assertEquals(21.1, $fs->getFuelEnergy('gas_e', 2));
        $this->assertEquals(77.7, $fs->getFuelEnergy('gas_ls', 10));
        $this->assertEquals(86.1, $fs->getFuelEnergy('gas_lw', 10));
        $this->assertEquals(23400, $fs->getFuelEnergy('wood', 10));
        $this->assertEquals(3, $fs->getFuelEnergy('electricity', 3));

        $this->setExpectedException('InvalidArgumentException');
        $this->assertEquals(10, $fs->getFuelEnergy('shmoal', 10));
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
