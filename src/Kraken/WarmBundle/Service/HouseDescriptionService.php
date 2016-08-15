<?php

namespace Kraken\WarmBundle\Service;

class HouseDescriptionService
{
    protected $instance;
    protected $dimensions;
    protected $floors;

    protected $houseTypes = [
        'single_house' => 'Dom jednorodzinny',
        'double_house' => 'Bliźniak',
        'row_house' => 'Dom szeregowy',
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
        'oblique' => 'dach skośny bez poddasza',
        'steep' => 'dach skośny z poddaszem',
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

    public function __construct(InstanceService $instance, DimensionsService $dimensions, FloorsService $floors)
    {
        $this->instance = $instance;
        $this->dimensions = $dimensions;
        $this->floors = $floors;
    }

    public function getHeadline()
    {
        $type = $this->instance->get()->getBuildingType();
        $house = $this->instance->get()->getHouse();

        $nbFloors = $house->getBuildingFloors();

        if ($type == 'apartment') {
            $floor = $nbFloors > 1 ? $nbFloors.'-poziomowe' : '';
        } else {
            $floors = [
                1 => 'parterowy',
                2 => 'jednopiętrowy',
                3 => 'dwupiętrowy',
                4 => 'trzypiętrowy',
                5 => 'czteropiętrowy',
            ];

            $floor = isset($floors[$nbFloors]) ? $floors[$nbFloors] : $nbFloors.'-piętrowy';

            if ($house->hasBasement()) {
                $floor .= ' podpiwniczony';
            }
        }

        return $this->houseTypes[$type].' '.$floor;
    }

    public function getHeatedAreaDescription()
    {
        $text = sprintf('%dm<sup>2</sup>', ceil($this->dimensions->getHeatedHouseArea()));

        $floorsDetails = $this->getHeatedFloorsDetails();

        if ($floorsDetails) {
            $text .= sprintf(' (%s)', $floorsDetails);
        }

        return $text;
    }

    public function getIsolationQualityDescription()
    {
        $house = $this->instance->get()->getHouse();
        $wallInternalIsolationSize = $house->getInternalIsolationLayer() && !stristr($house->getInternalIsolationLayer()->getMaterial()->getName(), 'pustka')
            ? $house->getInternalIsolationLayer()->getSize()
            : 0;
        $wallExternalIsolationSize = $house->getExternalIsolationLayer() ? $house->getExternalIsolationLayer()->getSize() : 0;
        $topIsolationSize = $house->getTopIsolationLayer() ? $house->getTopIsolationLayer()->getSize() : 0;
        $bottomIsolationSize = $house->getBottomIsolationLayer() ? $house->getBottomIsolationLayer()->getSize() : 0;
        $wallIsolationSize = $wallInternalIsolationSize + $wallExternalIsolationSize;

        $quality = 'przeciętna';

        if ($wallIsolationSize == 0 && $topIsolationSize == 0 && $bottomIsolationSize == 0) {
            $quality = 'fatalna';
        }
        if ($wallIsolationSize == 0) {
            $quality = 'kiepska';
        }
        if ($wallIsolationSize < 10) {
            $quality = 'przeciętna';
        }
        if ($wallIsolationSize >= 10 && $topIsolationSize > 0) {
            $quality = 'dobra';
        }
        if ($wallIsolationSize >= 10 && $topIsolationSize >= 10 && $bottomIsolationSize > 0) {
            $quality = 'bardzo dobra';
        }
        if ($wallIsolationSize >= 15 && $topIsolationSize >= 30 && $bottomIsolationSize >= 10) {
            $quality = 'znakomita';
        }

        $description = sprintf('ściany (%s), %s (%s), %s (%s)',
            $wallIsolationSize ? sprintf('%dcm', $wallIsolationSize) : '<strong>brak</strong>',
            strtolower($this->floors->getTopLabel()),
            $topIsolationSize ? sprintf('%dcm', $topIsolationSize) : '<strong>brak</strong>',
            strtolower($this->floors->getBottomLabel()),
            $bottomIsolationSize ? sprintf('%dcm', $bottomIsolationSize) : '<strong>brak</strong>'
        );

        return sprintf('<strong>%s</strong> &mdash; %s', $quality, $description);
    }

    public function getAreaDetails()
    {
        $house = $this->instance->get()->getHouse();
        $apartment = $house->getApartment();

        if ($apartment) {
            return sprintf('%sm<sup>2</sup>', ceil($this->dimensions->getHeatedHouseArea()));
        }

        return sprintf('ogrzewana: %sm<sup>2</sup>, całkowita: %sm<sup>2</sup>', ceil($this->dimensions->getHeatedHouseArea()), ceil($this->dimensions->getTotalHouseArea()));
    }

    public function getHeatedFloorsDetails()
    {
        $floors = $this->getFloors();

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
        $floors = $this->getFloors();

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
        $wallDetails = [];
        $isolationDetails = [];

        if ($house->getConstructionType() == 'canadian') {
            $wallDetails[] = 'szkielet drewniany (dom kanadyjski)';
        } else {
            $wallDetails[] = $house->getPrimaryWallMaterial()->getName();

            if ($house->getSecondaryWallMaterial()) {
                $wallDetails[] = strtolower($house->getSecondaryWallMaterial()->getName());
            }
        }

        if ($house->getInternalIsolationLayer()) {
            $isolationDetails[] = sprintf('%s %scm', $house->getInternalIsolationLayer()->getMaterial()->getName(), $house->getInternalIsolationLayer()->getSize());
        }

        if ($house->getExternalIsolationLayer()) {
            $isolationDetails[] = sprintf('%s %scm', $house->getExternalIsolationLayer()->getMaterial()->getName(), $house->getExternalIsolationLayer()->getSize());
        }

        if (empty($isolationDetails)) {
            $isolationDetails[] = 'brak';
        }

        return strtolower(sprintf('%scm, konstrukcja: %s, izolacja: %s', $house->getWallSize(), implode(' + ', $wallDetails), implode(' + ', $isolationDetails)));
    }

    public function getRoofDetails()
    {
        $house = $this->instance->get()->getHouse();

        if (!$this->instance->get()->isApartment()) {
            $roofInformation = [$this->floors->getTopLabel()];
        }

        if (($isolation = $house->getTopIsolationLayer()) != null) {
            $roofInformation[] = sprintf('%s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
        } else {
            $roofInformation[] = 'bez izolacji';
        }

        return implode(', ', $roofInformation);
    }

    public function getRoofType()
    {
        $house = $this->instance->get()->getHouse();

        return $this->roofTypes[$house->getBuildingRoof()];
    }

    public function getGroundDetails()
    {
        $house = $this->instance->get()->getHouse();

        $groundInformation = [$this->floors->getBottomLabel()];

        if (($isolation = $house->getBottomIsolationLayer()) != null) {
            $groundInformation[] = sprintf('%s %scm', $isolation->getMaterial()->getName(), $isolation->getSize());
        } else {
            $groundInformation[] = 'bez izolacji';
        }

        return implode(', ', $groundInformation);
    }

    public function getDoorsWindowsDetails()
    {
        $house = $this->instance->get()->getHouse();

        return sprintf(
            'Okna: %dszt. (%s)<br/>Drzwi: %dszt. (%s)',
            $house->getNumberWindows(),
            strtolower($this->windowsTypes[$house->getWindowsType()]),
            $house->getNumberDoors(),
            strtolower($this->doorsTypes[$house->getDoorsType()])
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
            $overUnder,
        ];

        return implode('<br />', $result);
    }

    public function getFloors()
    {
        $allFloors = $this->floors->getAllFloors();
        $heatedFloors = $this->instance->get()->getHouse()->getBuildingHeatedFloors();

        $floors = [];

        foreach ($allFloors as $floorIndex) {
            if ($floorIndex == 0) {
                $name = 'basement';
                $label = 'Piwnica';
            } elseif ($floorIndex == 1) {
                $name = 'ground_floor';
                $label = 'Parter';
            } elseif ($floorIndex == $this->floors->getLastFloorIndex() && $this->instance->get()->getHouse()->getBuildingRoof() == 'steep') {
                $name = 'attic';
                $label = 'Poddasze';
            } else {
                $name = 'regular_floor_'.($floorIndex - 1);
                $label = ($floorIndex - 1).'. piętro';
            }

            $floors[] = [
                'name' => $name,
                'label' => $label,
                'heated' => in_array($floorIndex, $heatedFloors),
            ];
        }

        return $floors;
    }
}
