<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Service\InstanceService;

class AdviceGenerator
{
    protected $building;
    protected $calculator;
    protected $instance;

    public function __construct(InstanceService $instance, BuildingInterface $building, EnergyCalculator $calculator)
    {
        $this->building = $building;
        $this->calculator = $calculator;
        $this->instance = $instance;
    }

    public function getAdvice()
    {
        $advice = array();

        $heatingPower = $this->calculator->getMaxHeatingPower();

        try {
            $fuelType = $this->instance->get()->getFuelType();
            $usingSolidFuel = stripos($fuelType, 'gas') === false && $fuelType != 'electricity';
            $stoveEfficiency = $this->calculator->getYearlyStoveEfficiency();

            if ($stoveEfficiency < 0.4) {
                if ($usingSolidFuel) {
                    $piece = 'Aktualnie większość pieniędzy wyrzucasz w atmosferę. ';
                    if (in_array($this->instance->get()->getStoveType(), array('', 'manual_upward'))) {
                        $piece .= '<a href="http://czysteogrzewanie.pl/jak-palic-w-piecu" target="_blank">Wypróbuj palenie od góry</a> - będzie taniej i wygodniej.';
                    }
                    $advice['Naucz się palić albo kup kocioł podajnikowy'] = $piece;
                }
            }

            if ($usingSolidFuel) {
                if ($stoveEfficiency > 0.6 && $stoveEfficiency < 0.9) {
                    $advice['Nie kupuj nowego kotła zasypowego'] = 'Obecny pracuje ze znakomitą sprawnością, a nowy kocioł zasypowy, zwłaszcza tani ulep z dmuchawą, może znacznie pogorszyć sytuację.';
                }
            } else {
                if ($stoveEfficiency > 0.85 && $stoveEfficiency < 1.2) {
                    $advice['Nie majstruj nic w ogrzewaniu, jeśli nie musisz'] = 'Obecna instalacja pracuje ze znakomitą sprawnością.';
                }
            }

            $stoveOversized = $this->calculator->isStoveOversized();

            if ($stoveOversized && $stoveEfficiency < 0.7 && $heatingPower > 10000) {
                $advice['Kup kocioł o mniejszej mocy'] = 'Obecny ma o wiele za dużą moc, przez co pożera bezproduktywnie nawet 2/3 opału.'.
                    ' Możesz też <a href="http://czysteogrzewanie.pl/zakupy/mocy-przybywaj-dobor-mocy-kotla-weglowego/#Co_zrobi_z_przewymiarowanym_kotem" target="_blank">rozwiązać problem tanio.</a>';
            }

        } catch (\Exception $e) {
            // skip if any case is not applicable
        }

        if ($heatingPower <= 8000) {
            $advice['Szkoda życia na szuflowanie węgla'] = "Twój dom jest energooszczędny, <a href='http://czysteogrzewanie.pl/2014/05/nie-pakuj-smieciucha-do-nowego-domu'>nie pakuj do niego 'śmieciucha'</a>! Nie oszczędzisz tyle ile myślisz, a uprzykrzysz sobie życie.";
        }

        return $advice;

    }
}
