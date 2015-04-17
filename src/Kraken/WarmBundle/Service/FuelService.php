<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Calculation;

class FuelService
{
    protected $fuelsEnergy = array(
        'coal' => 7.77,
        'coke' => 8,
        'sand_coal' => 6.66,
        'brown_coal' => 5,
        'pellet' => 5,
        'gas_e' => 10.55,
        'gas_ls' => 7.77,
        'gas_lw' => 8.61,
        'wood' => 5,
        'electricity' => 1,
    );

    /**
     * @param string $fuelType
     */
    public function getFuelEnergy($fuelType, $amount)
    {
        if (!isset($this->fuelsEnergy[$fuelType])) {
            throw new \InvalidArgumentException('Invalid fuel type');
        }

        return $this->fuelsEnergy[$fuelType] * $this->consumedAmountToBaseUnits($fuelType, $amount);
    }

    public function consumedAmountToBaseUnits($fuelType, $amount)
    {
        // tons to kgs
        if (in_array($fuelType, array('coal', 'coke', 'sand_coal', 'brown_coal', 'pellet'))) {
            return $amount * 1000;
        }

        // stere ~ 0,65 cubic meter
        if ($fuelType == 'wood') {
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
