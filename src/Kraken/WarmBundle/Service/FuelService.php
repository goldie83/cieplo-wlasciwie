<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;

class FuelService
{
    public function getFuelEnergy(Fuel $fuel, $amount)
    {
        return ($fuel->getEnergy() * 0.277) * $this->consumedAmountToBaseUnits($fuel, $amount);
    }

    public function consumedAmountToBaseUnits(Fuel $fuel, $amount)
    {
        // tons to kgs
        if (stristr($fuel->getType(), 'coal') || stristr($fuel->getType(), 'coke') || stristr($fuel->getType(), 'pellet')) {
            return $amount * 1000;
        }

        // stere ~ 0,65 cubic meter
        if ($fuel->getType() == 'wood') {
            return 0.65 * $amount * 720; // 720kg/
        }

        return $amount;
    }

    public function formatFuelAmount($amount, Calculation $calc)
    {
        $numbers = [
            'wood' => '%.1f',
            'gas_e' => '%d',
            'gas_ls' => '%d',
            'gas_lw' => '%d',
            'coke' => '%.1f',
            'sand_coal' => '%.1f',
            'pellet' => '%.1f',
            'electricity' => '%d',
            'brown_coal' => '%.1f',
            'coal' => '%.1f',
        ];

        $text = [
            'wood' => '%smp drewna',
            'gas_e' => '%sm<sup>3</sup> gazu ziemnego',
            'gas_ls' => '%sm<sup>3</sup> gazu ziemnego',
            'gas_lw' => '%sm<sup>3</sup> gazu ziemnego',
            'coke' => '%st koksu',
            'sand_coal' => '%st miału',
            'pellet' => '%st pelletu',
            'electricity' => '%skWh prądu',
            'brown_coal' => '%st węgla brunatnego',
            'coal' => '%st węgla kamiennego',
        ];

        $number = sprintf($numbers[$calc->getFuelType()], $amount);
        $number = str_replace('.', ',', $number);
        $number = str_replace(',0', '', $number);
        $text = $text[$calc->getFuelType()];

        return sprintf($text, $number);
    }
}
