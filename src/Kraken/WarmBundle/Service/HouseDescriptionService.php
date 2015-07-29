<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\Fuel;

class HouseDescriptionService
{
    protected $instance;
    protected $building;

    protected $houseTypes = [
        'single_house' => 'Budynek jednorodzinny',
        'double_house' => 'Bliźniak',
        'row_house' => 'Zabudowa szeregowa',
        'apartment' => 'Mieszkanie',
    ];

    protected $windowsTypes = [
        '' => '(nie podano)',
        'old_single_glass' => 'Stare z pojedynczą szybą',
        'old_double_glass' => 'Stare z min. dwiema szybami',
        'old_improved' => 'Stare, ale doszczelnione',
        'semi_new_double_glass' => 'Starsze niż 10-letnie z szybami zespolonymi',
        'new_double_glass' => 'Nowe z szybami zespolonymi',
        'new_triple_glass' => 'Nowe z trzema szybami',
    ];

    protected $doorsTypes = [
        '' => '(nie podano)',
        'old_wooden' => 'Stare drewniane',
        'old_metal' => 'Stare metalowe',
        'new_wooden' => 'Nowe drewniane',
        'new_metal' => 'Nowe metalowe',
        'other' => 'Inne',
    ];

    protected $roofTypes = [
        'flat' => 'dach płaski',
        'oblique' => 'dach dwuspadowy',
        'steep' => 'dach dwuspadowy stromy',
    ];

    protected $ventilationTypes = [
        'natural' => 'Naturalna lub grawitacyjna',
        'mechanical' => 'Mechaniczna',
        'mechanical_recovery' => 'Mechaniczna z odzyskiem ciepła',
    ];

    protected $whatsOverUnder = [
        'heated_room' => 'Ogrzewany lokal',
        'unheated_room' => 'Nieogrzewany lokal lub piwnica',
        'outdoor' => 'Świat zewnętrzny',
        'ground' => 'Grunt',
    ];

    public function __construct(InstanceService $instance, Building $building)
    {
        $this->instance = $instance;
        $this->building = $building;
    }

    public function getHeadline()
    {
        $type = $this->instance->get()->getBuildingType();
        $house = $this->instance->get()->getHouse();

        $sizes = sprintf('%sm x %sm w obrysie zewn.', $house->getBuildingLength(), $house->getBuildingWidth());
        $nbFloors = $house->getNumberFloors();

        if ($type == 'apartment') {
            $floor = $nbFloors.'-poziomowe';
        } else {
            $floors = array(
                1 => 'parterowy',
                2 => 'dwupiętrowy',
                3 => 'trzypiętrowy',
            );

            $floor = isset($floors[$nbFloors]) ? $floors[$nbFloors] : $nbFloors.'-piętrowy';

            if ($house->getHasBasement()) {
                $floor .= ' podpiwniczony';
            }
        }

        return $this->houseTypes[$type].' '.$floor.' A.D. '.$this->instance->get()->getConstructionYear().' ('.$sizes.')';
    }

    public function getAreaDetails()
    {
        $house = $this->instance->get()->getHouse();
        $apartment = $house->getApartment();

        if ($apartment) {
            return sprintf('%sm<sup>2</sup>', ceil($this->building->getHeatedHouseArea()));
        }

        return sprintf('ogrzewana: %sm<sup>2</sup>, całkowita: %sm<sup>2</sup>', ceil($this->building->getHeatedHouseArea()), ceil($this->building->getTotalHouseArea()));
    }

    public function getHeatedFloorsDetails()
    {
        $floors = $this->building->getFloors();

        $heatedFloors = [];
        foreach ($floors as $floor) {
            if ($floor['heated'] && $floor['label'] != 'other') {
                $heatedFloors[] = $floor['label'];
            }
        }

        return strtolower(implode(', ', $heatedFloors));
    }

    public function getUnheatedFloorsDetails()
    {
        $floors = $this->building->getFloors();

        $heatedFloors = [];
        foreach ($floors as $floor) {
            if ($floor['heated'] == false && $floor['label'] != 'other') {
                $heatedFloors[] = $floor['label'];
            }
        }

        return strtolower(implode(', ', $heatedFloors));
    }

    public function getWallDetails()
    {
        $house = $this->instance->get()->getHouse();
        $wall = $house->getWalls()->first();
        $wallDetails = [];
        $wallSize = 0;

        $includeLayer = function ($layer) use (&$wallDetails, &$wallSize) {
            if ($layer != null) {
                $wallDetails[] = sprintf('%s %scm', $layer->getMaterial()->getName(), $layer->getSize());
                $wallSize += $layer->getSize();
            }
        };

        if ($house->getConstructionType() == 'canadian') {
            $wallDetails[] = 'szkielet drewniany (dom kanadyjski)';
        } else {
            $includeLayer($wall->getConstructionLayer());
        }

        $includeLayer($wall->getIsolationLayer());
        $includeLayer($wall->getOutsideLayer());
        $includeLayer($wall->getExtraIsolationLayer());

        return sprintf('%scm, w tym %s', $wallSize, implode(' + ', $wallDetails));
    }

    public function getRoofDetails()
    {
        $house = $this->instance->get()->getHouse();
        $roofType = $house->getRoofType();

        $roofInformation = [$this->roofTypes[$roofType]];

        if (($isolation = $house->getRoofIsolationLayer()) != null) {
            $roofInformation[] = sprintf('izolacja: %s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
        } elseif (($isolation = $house->getHighestCeilingIsolationLayer()) != null) {
            $roofInformation[] = sprintf('izolacja: %s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
        }

        return implode(', ', $roofInformation);
    }

    public function getGroundDetails()
    {
        $house = $this->instance->get()->getHouse();

        if ($this->building->isBasementHeated()) {
            if (($isolation = $house->getBasementFloorIsolationLayer()) != null) {
                return sprintf('izolacja podłogi w piwnicy: %s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
            } else {
                return 'podgłoga w piwnicy bez izolacji';
            }
        } elseif ($this->building->isGroundFloorHeated()) {
            if (($isolation = $house->getGroundFloorIsolationLayer()) != null) {
                return sprintf('izolacja podłogi na gruncie: %s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
            } else {
                return 'podłoga na gruncie bez izolacji';
            }
        } else {
            if (($isolation = $house->getLowestCeilingIsolationLayer()) != null) {
                return sprintf('izolacja stropu nad nieogrzewanym parterem: %s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
            } else {
                return 'strop nad nieogrzewanym parterem bez izolacji';
            }
        }
    }

    public function getDoorsWindowsDetails()
    {
        $house = $this->instance->get()->getHouse();

        return sprintf(
            'Okna: %s (%s&nbsp;szt.), Drzwi: %s (%s&nbsp;szt.)', 
            strtolower($this->windowsTypes[$house->getWindowsType()]), 
            $house->getNumberWindows(), 
            strtolower($this->doorsTypes[$house->getDoorsType()]),
            $house->getNumberDoors()
        );
    }

    public function getVentilationDetails()
    {
        $house = $this->instance->get()->getHouse();

        return strtolower($this->ventilationTypes[$house->getVentilationType()]);
    }

    public function getApartmentSituationDetails()
    {
        $house = $this->instance->get()->getHouse();
        $apartment = $house->getApartment();

        if (!$apartment) {
            return '';
        }

        $nbExternalWalls = $apartment->getNumberExternalWalls();
        $externalWalls = 'Ściany zewnętrzne: '.($nbExternalWalls > 0 ? $nbExternalWalls : 'brak');

        $nbUnheatedWalls = $house->getApartment()->getNumberUnheatedWalls();
        $unheatedWalls = 'Ściany sąsiadujące z pomieszczeniami nieogrzewanymi: '.($nbUnheatedWalls > 0 ? $nbUnheatedWalls : 'brak');
        $overUnder = sprintf('Piętro wyżej: %s, piętro niżej: %s', strtolower($this->whatsOverUnder[$apartment->getWhatsOver()]), strtolower($this->whatsOverUnder[$apartment->getWhatsUnder()]));

        $result = [
            $externalWalls,
            $unheatedWalls,
            $overUnder
        ];

        return implode('<br />', $result);
    }
}
