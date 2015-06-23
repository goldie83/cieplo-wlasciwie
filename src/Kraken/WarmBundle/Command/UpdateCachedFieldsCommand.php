<?php

namespace Kraken\WarmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCachedFieldsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kraken:update-cached-fields')
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

            $session->set('calculation_id', $calc->getId());

            $calculator = $this->getContainer()->get('kraken_warm.energy_calculator');
            $building = $this->getContainer()->get('kraken_warm.building');

            $calc->setHeatedArea($building->getHeatedHouseArea());
            $calc->setHeatingPower($calculator->getMaxHeatingPower());
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
