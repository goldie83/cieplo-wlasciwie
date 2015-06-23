<?php

namespace Kraken\WarmBundle\Command;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;
use Kraken\WarmBundle\Entity\FuelConsumption;
use Kraken\WarmBundle\Entity\HeatingDevice;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kraken:migrate')
            ->setDescription('Blah')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $oldFuelTypes = [
            'wood' => Fuel::TYPE_WOOD,
            'gas_e' => Fuel::TYPE_NATURAL_GAS,
            'gas_ls' => Fuel::TYPE_NATURAL_GAS,
            'gas_lw' => Fuel::TYPE_NATURAL_GAS,
            'coke' => Fuel::TYPE_COKE,
            'sand_coal' => Fuel::TYPE_SAND_COAL,
            'pellet' => Fuel::TYPE_PELLET,
            'electricity' => Fuel::TYPE_ELECTRICITY,
            'brown_coal' => Fuel::TYPE_BROWN_COAL,
            'coal' => Fuel::TYPE_COAL,
        ];

        $oldStoveTypes = [
            'manual_upward' => HeatingDevice::TYPE_MANUAL_STOVE,
            'manual_downward' => HeatingDevice::TYPE_MANUAL_STOVE,
            'automatic' => HeatingDevice::TYPE_AUTOMATIC_STOVE,
            'fireplace' => HeatingDevice::TYPE_FIREPLACE,
            'kitchen' => HeatingDevice::TYPE_MASONRY_STOVE,
            'ceramic' => HeatingDevice::TYPE_MASONRY_STOVE,
            'goat' => HeatingDevice::TYPE_MANUAL_STOVE,
        ];

        $fuelMap = [];
        $deviceMap = [];

        $q = $em->createQuery('select f from KrakenWarmBundle:Fuel f');
        $iterableResult = $q->iterate();

        foreach ($iterableResult as $row) {
            $fuel = $row[0];
            $fuelMap[$fuel->getType()] = $fuel->getId();
        }

        $q = $em->createQuery('select d from KrakenWarmBundle:HeatingDevice d');
        $iterableResult = $q->iterate();

        foreach ($iterableResult as $row) {
            $device = $row[0];
            $deviceMap[$device->getType()] = $device->getId();
        }

        $batchSize = 20;
        $i = 0;
        $q = $em->createQuery('select c from KrakenWarmBundle:Calculation c');
        $iterableResult = $q->iterate();

        foreach ($iterableResult as $row) {
            $calc = $row[0];

            $oldStoveType = $calc->getStoveType();
            $oldFuelType = $calc->getFuelType();
            $oldFuelConsumption = $calc->getFuelConsumption();
            $oldFuelCost = $calc->getOldFuelCost();

            if ($oldStoveType && isset($oldStoveTypes[$oldStoveType])) {
                $calc->setHeatingDevice($em->getRepository('KrakenWarmBundle:HeatingDevice')->find($deviceMap[$oldStoveTypes[$oldStoveType]]));
            }

            if ($oldFuelType && isset($oldFuelTypes[$oldFuelType]) && $oldFuelConsumption) {
                $fc = new FuelConsumption;
                $fc->setCalculation($calc);
                $fc->setFuel($em->getRepository('KrakenWarmBundle:Fuel')->find($fuelMap[$oldFuelTypes[$oldFuelType]]));
                $fc->setConsumption($oldFuelConsumption);
                $fc->setCost($oldFuelCost);
                
                $em->persist($fc);
            }

            $em->persist($calc);

            if (($i % $batchSize) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();
    }
}
