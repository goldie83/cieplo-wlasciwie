<?php

namespace Kraken\WarmBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\WarmBundle\Entity\Fuel;
use Kraken\WarmBundle\Entity\HeatingDevice;
use Kraken\WarmBundle\Entity\HeatingVariant;

class LoadFuelData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $brownCoal = new Fuel();
        $brownCoal
            ->setType(Fuel::TYPE_BROWN_COAL)
            ->setName('Węgiel brunatny (lub czeski)')
            ->setPrice(0.5)
            ->setUnit('kg')
            ->setTradeAmount(1000)
            ->setTradeUnit('t')
            ->setEnergy(19)
        ;
        $manager->persist($brownCoal);

        $bituminousCoal = new Fuel();
        $bituminousCoal
            ->setType(Fuel::TYPE_COAL)
            ->setName('Węgiel kamienny')
            ->setPrice(0.75)
            ->setUnit('kg')
            ->setTradeAmount(1000)
            ->setTradeUnit('t')
            ->setEnergy(28)
        ;
        $manager->persist($bituminousCoal);

        $sandCoal = new Fuel();
        $sandCoal
            ->setType(Fuel::TYPE_SAND_COAL)
            ->setName('Miał węglowy')
            ->setPrice(0.6)
            ->setUnit('kg')
            ->setTradeAmount(1000)
            ->setTradeUnit('t')
            ->setEnergy(20)
        ;
        $manager->persist($sandCoal);

        $ecoCoal = new Fuel();
        $ecoCoal
            ->setType(Fuel::TYPE_ECO_COAL)
            ->setName('Ekogroszek')
            ->setPrice(0.9)
            ->setUnit('kg')
            ->setTradeAmount(1000)
            ->setTradeUnit('t')
            ->setEnergy(28)
        ;
        $manager->persist($ecoCoal);

        $naturalGas = new Fuel();
        $naturalGas
            ->setType(Fuel::TYPE_NATURAL_GAS)
            ->setName('Gaz ziemny')
            ->setPrice(0.22)
            ->setUnit('kWh')
            ->setTradeAmount(1)
            ->setTradeUnit('kWh')
            ->setEnergy(3.6)
        ;
        $manager->persist($naturalGas);

        $pellet = new Fuel();
        $pellet
            ->setType(Fuel::TYPE_PELLET)
            ->setName('Pellet')
            ->setPrice(0.9)
            ->setUnit('kg')
            ->setTradeAmount(1000)
            ->setTradeUnit('t')
            ->setEnergy(18)
        ;
        $manager->persist($pellet);

        $electricity = new Fuel();
        $electricity
            ->setType(Fuel::TYPE_ELECTRICITY)
            ->setName('Prąd')
            ->setPrice(0.3)
            ->setUnit('kWh')
            ->setTradeAmount(1)
            ->setTradeUnit('kWh')
            ->setEnergy(3.6)
        ;
        $manager->persist($electricity);

        $wood = new Fuel();
        $wood
            ->setType(Fuel::TYPE_WOOD)
            ->setName('Drewno')
            ->setPrice(0.35)
            ->setUnit('kg')
            ->setTradeAmount(450)
            ->setTradeUnit('mp')
            ->setEnergy(16)
        ;
        $manager->persist($wood);

        $coke = new Fuel();
        $coke
            ->setType(Fuel::TYPE_COKE)
            ->setName('Koks')
            ->setPrice(1)
            ->setUnit('kg')
            ->setTradeAmount(1000)
            ->setTradeUnit('t')
            ->setEnergy(30)
        ;
        $manager->persist($coke);

        $propane = new Fuel();
        $propane
            ->setType(Fuel::TYPE_PROPANE)
            ->setName('Propan (LPG)')
            ->setPrice(2.5)
            ->setUnit('l')
            ->setTradeAmount(1)
            ->setTradeUnit('l')
            ->setEnergy(24)
        ;
        $manager->persist($propane);

        $networkHeat = new Fuel();
        $networkHeat
            ->setType(Fuel::TYPE_NETWORK_HEAT)
            ->setName('Ciepło sieciowe')
            ->setPrice(0)
            ->setUnit('kWh')
            ->setTradeAmount(1)
            ->setTradeUnit('kWh')
            ->setEnergy(3.6)
        ;
        $manager->persist($networkHeat);

        $heatBuffer = new HeatingDevice();
        $heatBuffer
            ->setType(HeatingDevice::TYPE_HEAT_BUFFER)
            ->setName('Bufor ciepła')
            ->setForLegacySetup(false)
            ->setForAdvice(true)
        ;
        $manager->persist($heatBuffer);

        $manualStove = new HeatingDevice();
        $manualStove
            ->setType(HeatingDevice::TYPE_MANUAL_STOVE)
            ->setName('Kocioł zasypowy')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($manualStove);

        $manualStoveWithBuffer = new HeatingDevice();
        $manualStoveWithBuffer
            ->setType(HeatingDevice::TYPE_MANUAL_STOVE_BUFFER)
            ->setName('Kocioł zasypowy z buforem ciepła')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($manualStoveWithBuffer);

        $heatPumpAir = new HeatingDevice();
        $heatPumpAir
            ->setType(HeatingDevice::TYPE_HEAT_PUMP_AIR)
            ->setName('Pompa ciepła powietrzna')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($heatPumpAir);

        $heatPumpGround = new HeatingDevice();
        $heatPumpGround->setType(HeatingDevice::TYPE_HEAT_PUMP_GROUND)
            ->setName('Pompa ciepła gruntowa')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($heatPumpGround);

        $pelletStove = new HeatingDevice();
        $pelletStove
            ->setType(HeatingDevice::TYPE_PELLET_STOVE)
            ->setName('Kocioł na pellet')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($pelletStove);

        $holzgasStove = new HeatingDevice();
        $holzgasStove
            ->setType(HeatingDevice::TYPE_HOLZGAS_STOVE)
            ->setName('Kocioł zgazowujący drewno')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($holzgasStove);

        $automaticStove = new HeatingDevice();
        $automaticStove
            ->setType(HeatingDevice::TYPE_AUTOMATIC_STOVE)
            ->setName('Kocioł podajnikowy')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($automaticStove);

        $tileStove = new HeatingDevice();
        $tileStove
            ->setType(HeatingDevice::TYPE_MASONRY_STOVE)
            ->setName('Piec ceramiczny (kaflowy, kuchenny)')
            ->setForLegacySetup(true)
            ->setForAdvice(false)
        ;
        $manager->persist($tileStove);

        $fireplace = new HeatingDevice();
        $fireplace
            ->setType(HeatingDevice::TYPE_FIREPLACE)
            ->setName('Kominek')
            ->setForLegacySetup(true)
            ->setForAdvice(false)
        ;
        $manager->persist($fireplace);

        $electricStove = new HeatingDevice();
        $electricStove
            ->setType(HeatingDevice::TYPE_ELECTRIC_STOVE)
            ->setName('Elektryczny piecyk / bufor wodny grzany prądem')
            ->setForLegacySetup(true)
            ->setForAdvice(false)
        ;
        $manager->persist($electricStove);

        $condensing = new HeatingDevice();
        $condensing
            ->setType(HeatingDevice::TYPE_GAS_STOVE_CONDENSING)
            ->setName('Kocioł gazowy kondensacyjny')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($condensing);

        $gasStove = new HeatingDevice();
        $gasStove
            ->setType(HeatingDevice::TYPE_GAS_STOVE)
            ->setName('Kocioł gazowy niekondensacyjny')
            ->setForLegacySetup(true)
            ->setForAdvice(true)
        ;
        $manager->persist($gasStove);

        $oldGasStove = new HeatingDevice();
        $oldGasStove
            ->setType(HeatingDevice::TYPE_GAS_STOVE_OLD)
            ->setName('Kocioł gazowy niekondensacyjny starego typu')
            ->setForLegacySetup(true)
            ->setForAdvice(false)
        ;
        $manager->persist($oldGasStove);

        $heatingNetwork = new HeatingDevice();
        $heatingNetwork
            ->setType(HeatingDevice::TYPE_HEATING_NETWORK)
            ->setName('Sieć ciepłownicza / lokalna kotłownia')
            ->setForLegacySetup(true)
            ->setForAdvice(false)
        ;
        $manager->persist($heatingNetwork);

        $hv1 = new HeatingVariant();
        $hv1->setFuel($bituminousCoal);
        $hv1->setName('Węgiel kamienny');
        $hv1->setDetail('Spalany ekonomicznie w kotle zasypowym');
        $hv1->setHeatingDevice($manualStove);
        $hv1->setEfficiency(0.55);
        $hv1->setSetupCost(4000);
        $hv1->setMaintenanceTime(150);
        $manager->persist($hv1);

        $hv2 = new HeatingVariant();
        $hv2->setFuel($brownCoal);
        $hv2->setName('Węgiel czeski');
        $hv2->setDetail('Spalany ekonomicznie w kotle zasypowym');
        $hv2->setHeatingDevice($manualStove);
        $hv2->setEfficiency(0.55);
        $hv2->setSetupCost(4000);
        $hv2->setMaintenanceTime(150);
        $manager->persist($hv2);

        $hv3 = new HeatingVariant();
        $hv3->setFuel($wood);
        $hv3->setName('Drewno bukowe');
        $hv3->setDetail('Spalane ekonomicznie w kotle zasypowym');
        $hv3->setHeatingDevice($manualStove);
        $hv3->setSetupCost(4000);
        $hv3->setMaintenanceTime(150);
        $hv3->setEfficiency(0.5);
        $manager->persist($hv3);

        $hv11 = new HeatingVariant();
        $hv11->setFuel($bituminousCoal);
        $hv11->setName('Węgiel (+ bufor ciepła)');
        $hv11->setDetail('Spalany w kotle zasypowym z buforem ciepła');
        $hv11->setHeatingDevice($manualStoveWithBuffer);
        $hv11->setEfficiency(0.7);
        $hv11->setSetupCost(8000);
        $hv11->setMaintenanceTime(80);
        $manager->persist($hv11);

        $hv33 = new HeatingVariant();
        $hv33->setFuel($wood);
        $hv33->setName('Drewno (+ bufor ciepła)');
        $hv33->setDetail('Spalane w kotle zasypowym z buforem ciepła');
        $hv33->setHeatingDevice($manualStoveWithBuffer);
        $hv33->setEfficiency(0.7);
        $hv33->setSetupCost(8000);
        $hv33->setMaintenanceTime(80);
        $manager->persist($hv33);

        $hv4 = new HeatingVariant();
        $hv4->setFuel($sandCoal);
        $hv4->setName('Miał');
        $hv4->setDetail('Spalany ekonomicznie w kotle zasypowym');
        $hv4->setHeatingDevice($manualStove);
        $hv4->setEfficiency(0.6);
        $hv4->setSetupCost(5000);
        $hv4->setMaintenanceTime(150);
        $manager->persist($hv4);

        $hv5 = new HeatingVariant();
        $hv5->setFuel($coke);
        $hv5->setName('Koks');
        $hv5->setDetail('Spalany w kotle zasypowym');
        $hv5->setHeatingDevice($manualStove);
        $hv5->setEfficiency(0.7);
        $hv5->setSetupCost(4000);
        $hv5->setMaintenanceTime(120);
        $manager->persist($hv5);

        $hv6 = new HeatingVariant();
        $hv6->setFuel($ecoCoal);
        $hv6->setName('Kocioł podajnikowy na ekogroszek');
        $hv6->setDetail('Ekogroszek spalany w kotle podajnikowym');
        $hv6->setHeatingDevice($automaticStove);
        $hv6->setEfficiency(0.7);
        $hv6->setSetupCost(10000);
        $hv6->setMaintenanceTime(50);
        $manager->persist($hv6);

        $hv7 = new HeatingVariant();
        $hv7->setFuel($pellet);
        $hv7->setName('Pellet');
        $hv7->setDetail('Spalany w kotle na pellet z palnikiem wrzutkowym');
        $hv7->setHeatingDevice($pelletStove);
        $hv7->setEfficiency(0.8);
        $hv7->setSetupCost(12000);
        $hv7->setMaintenanceTime(30);
        $manager->persist($hv7);

        $hv8 = new HeatingVariant();
        $hv8->setFuel($naturalGas);
        $hv8->setName('Gaz ziemny + zwykły kocioł');
        $hv8->setDetail('Spalany w niekondensacyjnym nowym kotle gazowym');
        $hv8->setHeatingDevice($gasStove);
        $hv8->setEfficiency(0.85);
        $hv8->setSetupCost(6000);
        $hv8->setMaintenanceTime(0);
        $manager->persist($hv8);

        $hv9 = new HeatingVariant();
        $hv9->setFuel($naturalGas);
        $hv9->setName('Gaz ziemny + stary kocioł');
        $hv9->setDetail('Spalany w niekondensacyjnym starym kotle gazowym');
        $hv9->setHeatingDevice($oldGasStove);
        $hv9->setEfficiency(0.7);
        $hv9->setSetupCost(6000);
        $hv9->setMaintenanceTime(0);
        $hv9->setLegacy(true);
        $manager->persist($hv9);

        $hv91 = new HeatingVariant();
        $hv91->setFuel($naturalGas);
        $hv91->setName('Piec kaflowy');
        $hv91->setHeatingDevice($tileStove);
        $hv91->setEfficiency(0.6);
        $hv91->setMaintenanceTime(150);
        $hv91->setLegacy(true);
        $manager->persist($hv91);

        $hv92 = new HeatingVariant();
        $hv92->setFuel($naturalGas);
        $hv92->setName('Kominek');
        $hv92->setHeatingDevice($fireplace);
        $hv92->setEfficiency(0.5);
        $hv92->setMaintenanceTime(150);
        $hv92->setLegacy(true);
        $manager->persist($hv92);

        $hv10 = new HeatingVariant();
        $hv10->setFuel($naturalGas);
        $hv10->setName('Gaz ziemny + kondensat');
        $hv10->setDetail('Gaz ziemny typ E (GZ-50) spalany w kotle kondensacyjnym');
        $hv10->setHeatingDevice($condensing);
        $hv10->setEfficiency(1.04);
        $hv10->setSetupCost(8000);
        $hv10->setMaintenanceTime(0);
        $manager->persist($hv10);

        $hv11 = new HeatingVariant();
        $hv11->setFuel($propane);
        $hv11->setName('Propan + kondensat');
        $hv11->setDetail('Gaz płynny (propan/LPG) spalany w kotle kondensacyjnym');
        $hv11->setHeatingDevice($condensing);
        $hv11->setEfficiency(1.04);
        $hv11->setSetupCost(8000);
        $hv11->setMaintenanceTime(0);
        $manager->persist($hv11);

        $hv13 = new HeatingVariant();
        $hv13->setFuel($electricity);
        $hv13->setName('Bufor ciepła grzany prądem');
        $hv13->setHeatingDevice($heatBuffer);
        $hv13->setEfficiency(1);
        $hv13->setSetupCost(8000);
        $hv13->setMaintenanceTime(0);
        $manager->persist($hv13);

        $hv14 = new HeatingVariant();
        $hv14->setFuel($electricity);
        $hv14->setName('Pompa ciepła (powietrzna)');
        $hv14->setHeatingDevice($heatPumpAir);
        $hv14->setEfficiency(2.5);
        $hv14->setSetupCost(15000);
        $hv14->setMaintenanceTime(0);
        $manager->persist($hv14);

        $hv15 = new HeatingVariant();
        $hv15->setFuel($electricity);
        $hv15->setName('Pompa ciepła (gruntowa)');
        $hv15->setHeatingDevice($heatPumpGround);
        $hv15->setEfficiency(4);
        $hv15->setSetupCost(30000);
        $hv15->setMaintenanceTime(0);
        $manager->persist($hv15);

        $hv16 = new HeatingVariant();
        $hv16->setFuel($wood);
        $hv16->setName('Kocioł zgazowujący');
        $hv16->setDetail('Drewno bukowe spalane w kotle zgazowującym z buforem ciepła');
        $hv16->setHeatingDevice($holzgasStove);
        $hv16->setEfficiency(0.85);
        $hv16->setSetupCost(15000);
        $hv16->setMaintenanceTime(50);
        $manager->persist($hv16);

        $manager->flush();
    }
}
