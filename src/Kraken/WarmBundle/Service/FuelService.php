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
}
