<?php

namespace Kraken\WarmBundle\Command;

use Kraken\WarmBundle\Entity\Apartment;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Wall;
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

        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');
        $conn->executeUpdate('update house set number_heated_floors = ? where number_heated_floors < ? or number_heated_floors IS NULL', [1, 1]);

        $batchSize = 20;
        $i = 0;
        $q = $em->createQuery('select c from KrakenWarmBundle:Calculation c');
        $iterableResult = $q->iterate();

        $session = new Session();
        $session->start();

        foreach ($iterableResult as $row) {
            $calc = $row[0];

            if (!$calc->getHouse() instanceof House) {
                //                 $em->remove($calc);
                continue;
            }

            if ($calc->getBuildingType() == 'apartment' && !$calc->getHouse()->getApartment() instanceof Apartment) {
                //                 $em->remove($calc);
                continue;
            }

            $output->writeln(sprintf(
                    '<info>kraken:import</info> %s #%s lecim!',
                    $calc->getId(), base_convert($calc->getId(), 10, 36)
            ));

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

            $y = $calc->getConstructionYear();
            if ($y <= 1914) {
                $calc->setConstructionYear(1914);
            } elseif ($y <= 1939) {
                $calc->setConstructionYear(1939);
            } elseif ($y >= 1940 && $y < 1950) {
                $calc->setConstructionYear(1940);
            } elseif ($y >= 1950 && $y < 1960) {
                $calc->setConstructionYear(1950);
            } elseif ($y >= 1960 && $y < 1970) {
                $calc->setConstructionYear(1960);
            } elseif ($y >= 1970 && $y < 1980) {
                $calc->setConstructionYear(1970);
            } elseif ($y >= 1980 && $y < 1990) {
                $calc->setConstructionYear(1980);
            } elseif ($y >= 1990 && $y < 2000) {
                $calc->setConstructionYear(1990);
            } elseif ($y >= 2000 && $y <= 2010) {
                $calc->setConstructionYear(2000);
            } else {
                $calc->setConstructionYear(2011);
            }

            if ($calc->getHouse()->getConstructionType() != 'canadian') {
                $calc->getHouse()->setConstructionType('traditional');
            }

            $floorsNumber = $calc->getHouse()->getNumberFloors();
            $heatedFloorsNumber = $calc->getHouse()->getNumberHeatedFloors();
            $whatsUnheated = $calc->getHouse()->getWhatsUnheated();

            $totalFloors = $floorsNumber;

            if (!$calc->isApartment() && $calc->getHouse()->hasBasement()) {
                --$totalFloors;
            }

            if (!$calc->isApartment() && in_array($calc->getHouse()->getRoofType(), ['oblique', 'steep'])) {
                --$totalFloors;
            }

            $totalFloors = max(1, $totalFloors);

            $calc->getHouse()->setBuildingFloors($totalFloors);

            $heatedFloors = [];

            if ($calc->getHouse()->getNumberHeatedFloors() == 1 && $calc->getHouse()->getNumberFloors() == $calc->getHouse()->getNumberHeatedFloors()) {
                $calc->getHouse()->setBuildingFloors(1);
                $heatedFloors[] = 1;
                $calc->getHouse()->setHasBasement(false);
            } else {
                if ($calc->getHouse()->hasBasement() && ($whatsUnheated != 'basement' || $heatedFloorsNumber == $floorsNumber)) {
                    $heatedFloors[] = 0;
                }

                $floorsAboveGround = /*$calc->getHouse()->hasBasement() ? $totalFloors-1 : */$totalFloors;

                $currentFloor = 1;
                while ($currentFloor <= $floorsAboveGround) {
                    if ($currentFloor == 1 && $whatsUnheated == 'ground_floor') {
                        ++$currentFloor;
                        continue;
                    }
                    if ($currentFloor > 1 && $whatsUnheated == 'floor') {
                        ++$currentFloor;
                        continue;
                    }

                    $heatedFloors[] = $currentFloor;

                    ++$currentFloor;
                }

                if (!$calc->isApartment() && in_array($calc->getHouse()->getRoofType(), ['oblique', 'steep']) && ($whatsUnheated != 'attic' || $floorsNumber == $heatedFloorsNumber) && $calc->getHouse()->getNumberHeatedFloors() > count($heatedFloors)) {
                    $heatedFloors[] = $currentFloor;
                }
            }

            if ($heatedFloors == [0]) {
                $heatedFloors = [1];
            }

            while (count($heatedFloors) > $calc->getHouse()->getNumberHeatedFloors()) {
                unset($heatedFloors[count($heatedFloors) - 1]);
            }

            $calc->getHouse()->setBuildingHeatedFloors($heatedFloors);

            $calc->getHouse()->setBuildingRoof($calc->getHouse()->getRoofType() == 'flat' ? 'flat' : 'steep');
            $calc->getHouse()->setBuildingShape('regular');

            $wall = $calc->getHouse()->getWalls()->first();
            $wallSize = 0;

            if (!$wall instanceof Wall) {
                //                 $em->remove($calc);

                continue;
            }

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

            if ($wallSize == 0) {
                $output->writeln(sprintf(
                    '<error>kraken:import</error> #%s ŚCIANA U CYGANA',
                    base_convert($calc->getId(), 10, 36)
                ));
                continue;
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

            if (!$calc->getHouse()->hasGarage() && abs($dimensions->getHeatedHouseArea() - $calc->getHeatedArea()) > 0.2 * $calc->getHeatedArea()) {
                $em->flush();
                $output->writeln(sprintf(
                        '<error>kraken:import</error> #%s Metraż się sypnął: było %dmkw. a jest %dmkw.',
                        base_convert($calc->getId(), 10, 36), $calc->getHeatedArea(), $dimensions->getHeatedHouseArea()
                ));
            }

            if (!$calc->getHouse()->hasGarage() && abs($calculator->getMaxHeatingPower() - $calc->getHeatingPower()) > 0.3 * $calc->getHeatingPower()) {
                $em->flush();
                $output->writeln(sprintf(
                    '<error>kraken:import</error> #%s Moc się sypnęła: było %dkW a jest %dkW',
                    base_convert($calc->getId(), 10, 36), $calc->getHeatingPower(), $calculator->getMaxHeatingPower()
                ));
            }

            $output->writeln(sprintf(
                    '<info>kraken:import</info> %s #%s zrobione!',
                    $calc->getId(), base_convert($calc->getId(), 10, 36)
            ));

            if (($i % $batchSize) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();
    }
}
