<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;

class FuelService
{
    public function getFuelEnergy(Fuel $fuel, $amount)
    {
        // amount comes in megajoules, result goes in kWh

        return ceil(($fuel->getEnergy() * 0.277) * $this->consumedAmountToBaseUnits($fuel, $amount));
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

    public function formatFuelConsumption(Calculation $calc)
    {
        $numbers = [
            'wood' => '%.1f',
            'propane' => '%d',
            'natural_gas' => '%d',
            'coke' => '%.1f',
            'sand_coal' => '%.1f',
            'pellet' => '%.1f',
            'electricity' => '%d',
            'network_heat' => '%d',
            'brown_coal' => '%.1f',
            'eco_coal' => '%.1f',
            'bituminous_coal' => '%.1f',
        ];

        $text = [
            'wood' => '%smp drewna',
            'propane' => '%sl propanu',
            'natural_gas' => '%skWh gazu ziemnego',
            'coke' => '%st koksu',
            'sand_coal' => '%st miału',
            'pellet' => '%st pelletu',
            'electricity' => '%skWh prądu',
            'network_heat' => '%skWh energii cieplnej',
            'brown_coal' => '%st węgla brunatnego',
            'eco_coal' => '%st ekogroszku',
            'bituminous_coal' => '%st węgla kamiennego',
        ];

        $labels = [];

        foreach ($calc->getFuelConsumptions() as $fc) {
            if (!$fc->getFuel() || !$fc->getConsumption()) {
                continue;
            }

            $amount = round($fc->getConsumption(), 1);
            $number = sprintf($numbers[$fc->getFuel()->getType()], $amount);
            $number = str_replace('.', ',', $number);
            $number = str_replace(',0', '', $number);
            $label = $text[$fc->getFuel()->getType()];

            $labels[] = sprintf($label, $number);
        }

        return implode(', ', $labels);
    }
}
