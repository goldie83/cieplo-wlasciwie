<?php

namespace Kraken\WarmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kraken:import')
            ->setDescription('Blah')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $batchSize = 20;
        $i = 0;
        $q = $em->createQuery('select c from KrakenWarmBundle:Calculation c');
        $iterableResult = $q->iterate();

        $session = new Session();
        $session->start();

        foreach ($iterableResult as $row) {
            $calc = $row[0];

            $constructionYears = [
                1914 => 'gdzieś przed I wojną',
                1939 => 'gdzieś przed II wojną',
                1940 => 'lata 40-te',
                1950 => 'lata 50-te',
                1960 => 'lata 60-te',
                1970 => 'lata 70-te',
                1980 => 'lata 80-te',
                1990 => 'lata 90-te',
                2000 => 'lata 2000 – 2010',
                2011 => 'lata 2011 – 2016',
            ];

            if ($calc->getConstructionYear() <= 1914) {
                $calc->setConstructionYear(1914);
            } elseif ($calc->getConstructionYear() <= 1939) {
                $calc->setConstructionYear(1939);
            } else {
                $i = 0;
                foreach ($constructionYears as $year => $label) {
                    if ($calc->getConstructionYear() <= $year) {
                        $years = array_keys($constructionYears);
                        $calc->setConstructionYear($years[max(0, $i-1)]);
                        break;
                    }
                    $i++;
                }
            }

            //RRRWAAAA
            if ($calc->getHouse()->getConstructionType() != 'canadian') {
                $calc->getHouse()->setConstructionType('traditional');
            }

            $floorsNumber = $calc->getHouse()->getNumberFloors();
            $heatedFloorsNumber = $calc->getHouse()->getNumberHeatedFloors();
            $whatsUnheated = $calc->getHouse()->getWhatsUnheated();

            $totalFloors = $floorsNumber;

            if (!$calc->isApartment() && $calc->getHouse()->hasBasement()) {
                $totalFloors--;
            }

            if (!$calc->isApartment() && $calc->getHouse()->getRoofType() != 'flat') {
                $totalFloors--;
            }

            $calc->getHouse()->setBuildingFloors($totalFloors);


            $heatedFloors = [];
            for ($i = 0; $i < $floorsNumber; $i++) {
                $heatedFloors[] = $calc->getHouse()->hasBasement() ? $i : $i+1;
            }

            if ($floorsNumber - $heatedFloorsNumber == 1) {
                if ($whatsUnheated == 'basement') {
                    unset($heatedFloors[0]);
                } elseif ($whatsUnheated == 'attic') {
                    unset($heatedFloors[count($heatedFloors)-1]);
                } elseif ($whatsUnheated == 'floor' || $whatsUnheated == 'ground_floor') {
                    unset($heatedFloors[$calc->getHouse()->hasBasement() ? 1 : 0]);
                }
            } elseif ($floorsNumber - $heatedFloorsNumber == 2) {
                unset($heatedFloors[0]);
                unset($heatedFloors[count($heatedFloors)-1]);
            } else {
                $heatedFloors = [1];
            }

            $calc->getHouse()->setBuildingHeatedFloors($heatedFloors);

            $calc->getHouse()->setBuildingRoof($calc->getHouse()->getRoofType() == 'flat' ? 'flat' : 'steep');
            $calc->getHouse()->setBuildingShape('regular');


            $wall = $calc->getHouse()->getWalls()->first();
            $wallSize = 0;

            if ($wall->getConstructionLayer()) {
                $calc->getHouse()->setPrimaryWallMaterial($wall->getConstructionLayer()->getMaterial());
                $wallSize += $wall->getConstructionLayer()->getSize();
            }

            if ($wall->getOutsideLayer()) {
                $calc->getHouse()->setSecondaryWallMaterial($wall->getOutsideLayer()->getMaterial());
                $wallSize += $wall->getOutsideLayer()->getSize();
            }

            if ($wall->getIsolationLayer()) {
                $calc->getHouse()->setInternalIsolationLayer($wall->getIsolationLayer());
                $wallSize += $wall->getIsolationLayer()->getSize();
            }

            if ($wall->getExtraIsolationLayer()) {
                $calc->getHouse()->setExternalIsolationLayer($wall->getExtraIsolationLayer());
                $wallSize += $wall->getExtraIsolationLayer()->getSize();
            }

            $calc->getHouse()->setWallSize($wallSize);


            if ($calc->getHouse()->getBasementFloorIsolationLayer()) {
                $calc->getHouse()->setBottomIsolationLayer($calc->getHouse()->getBasementFloorIsolationLayer());
            } elseif ($calc->getHouse()->getGroundFloorIsolationLayer()) {
                $calc->getHouse()->setBottomIsolationLayer($calc->getHouse()->getGroundFloorIsolationLayer());
            } elseif ($calc->getHouse()->getLowestCeilingIsolationLayer()) {
                $calc->getHouse()->setBottomIsolationLayer($calc->getHouse()->getLowestCeilingIsolationLayer());
            }

            if ($calc->getHouse()->getRoofIsolationLayer()) {
                $calc->getHouse()->setTopIsolationLayer($calc->getHouse()->getRoofIsolationLayer());
            } elseif ($calc->getHouse()->getHighestCeilingIsolationLayer()) {
                $calc->getHouse()->setTopIsolationLayer($calc->getHouse()->getHighestCeilingIsolationLayer());
            }

            $em->persist($calc);

            $session->set('calculation_id', $calc->getId());

            $calculator = $this->getContainer()->get('kraken_warm.energy_calculator');
            $dimensions = $this->getContainer()->get('kraken_warm.dimensions');

            if (abs($dimensions->getHeatedHouseArea() - $calc->getHeatedArea()) > 0.2 * $calc->getHeatedArea()) {
                $em->flush();
                throw new \Exception(sprintf("#%d Metraż się sypnął: było %dmkw. a jest %dmkw.", $calc->getId(), $calc->getHeatedArea(), $dimensions->getHeatedHouseArea()));
            }

            if (abs($calculator->getMaxHeatingPower() - $calc->getHeatingPower()) > 0.2 * $calc->getHeatingPower()) {
                $em->flush();
                throw new \Exception(sprintf("#%d Moc się sypnęła: było %dkW a jest %dkW", $calc->getId(), $calc->getHeatingPower(), $calculator->getMaxHeatingPower()));
            }

            if (($i % $batchSize) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();
    }
}
