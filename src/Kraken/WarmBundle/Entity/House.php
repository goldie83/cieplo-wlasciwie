<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * @ORM\Entity
 * @ORM\Table(name="house")
 * @Assert\Callback(methods={"areDimensionsValid", "areWallsValid"})
 */
class House
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    protected $area;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     * @Assert\Range(min="1", minMessage="Porządny dom powinien mieć min. 1m szerokości")
     */
    protected $building_length;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     * @Assert\Range(min="1", minMessage = "Porządny dom powinien mieć min. 1m długości")
     */
    protected $building_width;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $building_shape;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    protected $building_contour_free_area;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $building_floors;

    /**
     * @ORM\Column(type="json_array")
     * @Assert\NotBlank(message="Przynajmniej jedno piętro powinno być ogrzewane")
     */
    protected $building_heated_floors;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $building_roof;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     * @Assert\Range(min="1", minMessage = "Nawet ziemianka ma min. 1 piętro", max="99", maxMessage = "Nie wierzę, że masz więcej niż 100 pięter w swojej chałupie")
     */
    protected $number_floors;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1)
     * @Assert\NotBlank
     */
    protected $floor_height;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     * @Assert\Range(min="1", minMessage = "Nawet ziemianka ma min. 1 piętro ogrzewane", max="99", maxMessage = "Za mało mam palców by to policzyć")
     */
    protected $number_heated_floors;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $whats_unheated;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $construction_type;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     * @Assert\Range(min="0", minMessage = "Za mało drzwi zewnętrznych", max="99", maxMessage = "Więcej jak 99 drzwi nie jest ci potrzebne. Sprzedaj połowę.")
     */
    protected $number_doors;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('old_wooden', 'old_metal', 'new_wooden', 'new_metal', 'other')")
     */
    protected $doors_type;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     * @Assert\Range(min="1", minMessage = "Min. 1 okno powinieneś posiadać", max="99", maxMessage = "Więcej jak 99 okien nie jest ci potrzebne. Sprzedaj połowę.")
     */
    protected $number_windows;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    protected $number_balcony_doors;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    protected $number_huge_glazings;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('old_single_glass', 'old_double_glass', 'old_improved', 'semi_new_double_glass', 'new_double_glass', 'new_triple_glass')")
     */
    protected $windows_type;

    /**
     * @ORM\OneToMany(targetEntity="Wall", mappedBy="house", cascade={"all"})
     * @Assert\Valid
     */
    protected $walls;

    /**
     * @ORM\Column(type="string",columnDefinition="ENUM('flat', 'oblique', 'steep')")
     */
    protected $roof_type;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="highest_ceiling_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $highest_ceiling_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="roof_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $roof_isolation_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_balcony;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_basement;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="ground_floor_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $ground_floor_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="basement_floor_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $basement_floor_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="lowest_ceiling_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $lowest_ceiling_isolation_layer;

    /**
     * @ORM\Column(type="string",columnDefinition="ENUM('natural', 'mechanical', 'mechanical_recovery')")
     */
    protected $ventilation_type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_garage;

    /**
     * @ORM\OneToMany(targetEntity="Calculation", mappedBy="house", cascade={"all"})
     */
    protected $calculations;

    /**
     * @ORM\ManyToOne(targetEntity="Apartment", cascade={"all"}, inversedBy="houses")
     * @ORM\JoinColumn(name="apartment_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $apartment;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     * @Assert\Range(min="1", minMessage = "Min. grubość ściany to 1cm")
     */
    protected $wall_size;

    /**
     * @ORM\ManyToOne(targetEntity="Material")
     * @ORM\JoinColumn(name="primary_wall_material_id", referencedColumnName="id", nullable=true)
     */
    protected $primary_wall_material;

    /**
     * @ORM\ManyToOne(targetEntity="Material")
     * @ORM\JoinColumn(name="secondary_wall_material_id", referencedColumnName="id", nullable=true)
     */
    protected $secondary_wall_material;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="internal_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $internal_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="external_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $external_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="top_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $top_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="bottom_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $bottom_isolation_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_row_house_on_corner;

    public function areDimensionsValid(ExecutionContext $context)
    {
        if (!$this->area && !$this->building_length && !$this->building_width) {
            $context->addViolationAt('area', 'Podaj powierzchnię zabudowy lub wymiary obrysu budynku', [], null);
        }

        if ($this->building_length && !$this->building_width) {
            $context->addViolationAt('building_width', 'Podaj szerokość obrysu budynku', [], null);
        }

        if (!$this->building_length && $this->building_width) {
            $context->addViolationAt('building_length', 'Podaj długość obrysu budynku', [], null);
        }
    }

    public function areWallsValid(ExecutionContext $context)
    {
        if ($this->construction_type == 'traditional' && $this->primary_wall_material == null && $this->wall_size > 0) {
            $context->addViolationAt('primary_wall_material', 'Wybierz podstawowy materiał konstrukcyjny ścian zewnętrznych', [], null);
        }

        if ($this->construction_type == 'canadian' && !$this->internal_isolation_layer/* && !$this->internal_isolation_layer->getMaterial()*/) {
            $context->addViolationAt('internal_isolation_layer', 'Dla domu szkieletowego musisz podać jaki materiał izolacyjny wypełnia ściany', [], null);
        }
    }

    public static function create()
    {
        $house = new self();

        return $house;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->walls = new \Doctrine\Common\Collections\ArrayCollection();
        $this->floor_height = 2.6;
        $this->building_floors = 1;
        $this->building_roof = 'steep';
        $this->building_heated_floors = [1, 2];
        $this->construction_type = 'traditional';
        $this->doors_type = 'new_wooden';
        $this->windows_type = 'new_double_glass';
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set building_length.
     *
     * @param int $buildingLength
     *
     * @return House
     */
    public function setBuildingLength($buildingLength)
    {
        $this->building_length = $buildingLength;

        return $this;
    }

    /**
     * Get building_length.
     *
     * @return int
     */
    public function getBuildingLength()
    {
        return $this->building_length;
    }

    /**
     * Set building_width.
     *
     * @param int $buildingWidth
     *
     * @return House
     */
    public function setBuildingWidth($buildingWidth)
    {
        $this->building_width = $buildingWidth;

        return $this;
    }

    /**
     * Get building_width.
     *
     * @return int
     */
    public function getBuildingWidth()
    {
        return $this->building_width;
    }

    /**
     * Set number_floors.
     *
     * @param int $numberFloors
     *
     * @return House
     */
    public function setNumberFloors($numberFloors)
    {
        $this->number_floors = $numberFloors;

        return $this;
    }

    /**
     * Get number_floors.
     *
     * @return int
     */
    public function getNumberFloors()
    {
        return $this->number_floors;
    }

    public function setFloorHeight($floorHeight)
    {
        $this->floor_height = $floorHeight;

        return $this;
    }

    public function getFloorHeight()
    {
        return $this->floor_height;
    }

    /**
     * Set number_doors.
     *
     * @param int $numberDoors
     *
     * @return House
     */
    public function setNumberDoors($numberDoors)
    {
        $this->number_doors = $numberDoors;

        return $this;
    }

    /**
     * Get number_doors.
     *
     * @return int
     */
    public function getNumberDoors()
    {
        return $this->number_doors;
    }

    /**
     * Set doors_type.
     *
     * @param string $doorsType
     *
     * @return House
     */
    public function setDoorsType($doorsType)
    {
        $this->doors_type = $doorsType;

        return $this;
    }

    /**
     * Get doors_type.
     *
     * @return string
     */
    public function getDoorsType()
    {
        return $this->doors_type;
    }

    /**
     * Set number_windows.
     *
     * @param int $numberWindows
     *
     * @return House
     */
    public function setNumberWindows($numberWindows)
    {
        $this->number_windows = $numberWindows;

        return $this;
    }

    /**
     * Get number_windows.
     *
     * @return int
     */
    public function getNumberWindows()
    {
        return $this->number_windows;
    }

    /**
     * Set windows_type.
     *
     * @param string $windowsType
     *
     * @return House
     */
    public function setWindowsType($windowsType)
    {
        $this->windows_type = $windowsType;

        return $this;
    }

    /**
     * Get windows_type.
     *
     * @return string
     */
    public function getWindowsType()
    {
        return $this->windows_type;
    }

    /**
     * Set roof_type.
     *
     * @param string $roofType
     *
     * @return House
     */
    public function setRoofType($roofType)
    {
        $this->roof_type = $roofType;

        return $this;
    }

    /**
     * Get roof_type.
     *
     * @return string
     */
    public function getRoofType()
    {
        return $this->roof_type;
    }

    /**
     * Set has_basement.
     *
     * @param bool $hasBasement
     *
     * @return House
     */
    public function setHasBasement($hasBasement)
    {
        $this->has_basement = $hasBasement;

        return $this;
    }

    /**
     * Get has_basement.
     *
     * @return bool
     */
    public function getHasBasement()
    {
        return $this->has_basement;
    }

    /**
     * Set ventilation_type.
     *
     * @param string $ventilationType
     *
     * @return House
     */
    public function setVentilationType($ventilationType)
    {
        $this->ventilation_type = $ventilationType;

        return $this;
    }

    /**
     * Get ventilation_type.
     *
     * @return string
     */
    public function getVentilationType()
    {
        return $this->ventilation_type;
    }

    /**
     * Set has_garage.
     *
     * @param bool $hasGarage
     *
     * @return House
     */
    public function setHasGarage($hasGarage)
    {
        $this->has_garage = $hasGarage;

        return $this;
    }

    /**
     * Get has_garage.
     *
     * @return bool
     */
    public function getHasGarage()
    {
        return $this->has_garage;
    }

    /**
     * Add walls.
     *
     * @param \Kraken\WarmBundle\Entity\Wall $walls
     *
     * @return House
     */
    public function addWall(\Kraken\WarmBundle\Entity\Wall $walls)
    {
        $this->walls[] = $walls;

        return $this;
    }

    /**
     * Remove walls.
     *
     * @param \Kraken\WarmBundle\Entity\Wall $walls
     */
    public function removeWall(\Kraken\WarmBundle\Entity\Wall $walls)
    {
        $this->walls->removeElement($walls);
    }

    /**
     * Get walls.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWalls()
    {
        return $this->walls;
    }

    /**
     * Set highest_ceiling_isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $highestCeilingIsolationLayer
     *
     * @return House
     */
    public function setHighestCeilingIsolationLayer(\Kraken\WarmBundle\Entity\Layer $highestCeilingIsolationLayer = null)
    {
        $this->highest_ceiling_isolation_layer = $highestCeilingIsolationLayer;

        return $this;
    }

    /**
     * Get highest_ceiling_isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getHighestCeilingIsolationLayer()
    {
        return $this->highest_ceiling_isolation_layer;
    }

    /**
     * Set roof_isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $roofIsolationLayer
     *
     * @return House
     */
    public function setRoofIsolationLayer(\Kraken\WarmBundle\Entity\Layer $roofIsolationLayer = null)
    {
        $this->roof_isolation_layer = $roofIsolationLayer;

        return $this;
    }

    /**
     * Get roof_isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getRoofIsolationLayer()
    {
        return $this->roof_isolation_layer;
    }

    /**
     * Set ground_floor_isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $groundFloorIsolationLayer
     *
     * @return House
     */
    public function setGroundFloorIsolationLayer(\Kraken\WarmBundle\Entity\Layer $groundFloorIsolationLayer = null)
    {
        $this->ground_floor_isolation_layer = $groundFloorIsolationLayer;

        return $this;
    }

    /**
     * Get ground_floor_isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getGroundFloorIsolationLayer()
    {
        return $this->ground_floor_isolation_layer;
    }

    /**
     * Set basement_floor_isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $basementFloorIsolationLayer
     *
     * @return House
     */
    public function setBasementFloorIsolationLayer(\Kraken\WarmBundle\Entity\Layer $basementFloorIsolationLayer = null)
    {
        $this->basement_floor_isolation_layer = $basementFloorIsolationLayer;

        return $this;
    }

    /**
     * Get basement_floor_isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getBasementFloorIsolationLayer()
    {
        return $this->basement_floor_isolation_layer;
    }

    /**
     * Set lowest_ceiling_isolation_layer.
     *
     * @param \Kraken\WarmBundle\Entity\Layer $lowestCeilingIsolationLayer
     *
     * @return House
     */
    public function setLowestCeilingIsolationLayer(\Kraken\WarmBundle\Entity\Layer $lowestCeilingIsolationLayer = null)
    {
        $this->lowest_ceiling_isolation_layer = $lowestCeilingIsolationLayer;

        return $this;
    }

    /**
     * Get lowest_ceiling_isolation_layer.
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getLowestCeilingIsolationLayer()
    {
        return $this->lowest_ceiling_isolation_layer;
    }

    /**
     * Add calculations.
     *
     * @param \Kraken\WarmBundle\Entity\Calculation $calculations
     *
     * @return House
     */
    public function addCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations[] = $calculations;

        return $this;
    }

    /**
     * Remove calculations.
     *
     * @param \Kraken\WarmBundle\Entity\Calculation $calculations
     */
    public function removeCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations->removeElement($calculations);
    }

    /**
     * Get calculations.
     *
     * @return Calculation[]
     */
    public function getCalculations()
    {
        return $this->calculations;
    }

    public function getCalculation()
    {
        return $this->calculations->first();
    }

    /**
     * Set has_balcony.
     *
     * @param bool $hasBalcony
     *
     * @return House
     */
    public function setHasBalcony($hasBalcony)
    {
        $this->has_balcony = $hasBalcony;

        return $this;
    }

    /**
     * Get has_balcony.
     *
     * @return bool
     */
    public function getHasBalcony()
    {
        return $this->has_balcony;
    }

    /**
     * Set apartment.
     *
     * @param \Kraken\WarmBundle\Entity\Apartment $apartment
     *
     * @return House
     */
    public function setApartment(\Kraken\WarmBundle\Entity\Apartment $apartment = null)
    {
        $this->apartment = $apartment;

        return $this;
    }

    /**
     * Get apartment.
     *
     * @return \Kraken\WarmBundle\Entity\Apartment
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * Set number_heated_floors.
     *
     * @param int $numberHeatedFloors
     *
     * @return House
     */
    public function setNumberHeatedFloors($numberHeatedFloors)
    {
        $this->number_heated_floors = $numberHeatedFloors;

        return $this;
    }

    /**
     * Get number_heated_floors.
     *
     * @return int
     */
    public function getNumberHeatedFloors()
    {
        return $this->number_heated_floors;
    }

    /**
     * Set whats_unheated.
     *
     * @param string $whatsUnheated
     *
     * @return House
     */
    public function setWhatsUnheated($whatsUnheated)
    {
        $this->whats_unheated = $whatsUnheated;

        return $this;
    }

    /**
     * Get whats_unheated.
     *
     * @return string
     */
    public function getWhatsUnheated()
    {
        return $this->whats_unheated;
    }

    public function getConstructionType()
    {
        return $this->construction_type;
    }

    public function setConstructionType($construction_type)
    {
        $this->construction_type = $construction_type;

        return $this;
    }

    /**
     * Set area
     *
     * @param string $area
     * @return House
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set building_shape
     *
     * @param string $buildingShape
     * @return House
     */
    public function setBuildingShape($buildingShape)
    {
        $this->building_shape = $buildingShape;

        return $this;
    }

    /**
     * Get building_shape
     *
     * @return string
     */
    public function getBuildingShape()
    {
        return $this->building_shape;
    }

    /**
     * Set building_contour_free_area
     *
     * @param string $buildingContourFreeArea
     * @return House
     */
    public function setBuildingContourFreeArea($buildingContourFreeArea)
    {
        $this->building_contour_free_area = $buildingContourFreeArea;

        return $this;
    }

    /**
     * Get building_contour_free_area
     *
     * @return string
     */
    public function getBuildingContourFreeArea()
    {
        return $this->building_contour_free_area;
    }

    /**
     * Set building_floors
     *
     * @param integer $buildingFloors
     * @return House
     */
    public function setBuildingFloors($buildingFloors)
    {
        $this->building_floors = $buildingFloors;

        return $this;
    }

    /**
     * Get building_floors
     *
     * @return integer
     */
    public function getBuildingFloors()
    {
        return $this->building_floors;
    }

    /**
     * Set building_heated_floors
     *
     * @param array $buildingHeatedFloors
     * @return House
     */
    public function setBuildingHeatedFloors($buildingHeatedFloors)
    {
        $this->building_heated_floors = $buildingHeatedFloors;

        return $this;
    }

    /**
     * Get building_heated_floors
     *
     * @return array
     */
    public function getBuildingHeatedFloors()
    {
        return $this->building_heated_floors;
    }

    /**
     * Set building_roof
     *
     * @param string $buildingRoof
     * @return House
     */
    public function setBuildingRoof($buildingRoof)
    {
        $this->building_roof = $buildingRoof;

        return $this;
    }

    /**
     * Get building_roof
     *
     * @return string
     */
    public function getBuildingRoof()
    {
        return $this->building_roof;
    }

    /**
     * Set primary_wall_material
     *
     * @param \Kraken\WarmBundle\Entity\Material $primaryWallMaterial
     * @return House
     */
    public function setPrimaryWallMaterial(\Kraken\WarmBundle\Entity\Material $primaryWallMaterial = null)
    {
        $this->primary_wall_material = $primaryWallMaterial;

        return $this;
    }

    /**
     * Get primary_wall_material
     *
     * @return \Kraken\WarmBundle\Entity\Material
     */
    public function getPrimaryWallMaterial()
    {
        return $this->primary_wall_material;
    }

    /**
     * Set secondary_wall_material
     *
     * @param \Kraken\WarmBundle\Entity\Material $secondaryWallMaterial
     * @return House
     */
    public function setSecondaryWallMaterial(\Kraken\WarmBundle\Entity\Material $secondaryWallMaterial = null)
    {
        $this->secondary_wall_material = $secondaryWallMaterial;

        return $this;
    }

    /**
     * Get secondary_wall_material
     *
     * @return \Kraken\WarmBundle\Entity\Material
     */
    public function getSecondaryWallMaterial()
    {
        return $this->secondary_wall_material;
    }

    /**
     * Set internal_isolation_layer
     *
     * @param \Kraken\WarmBundle\Entity\Layer $internalIsolationLayer
     * @return House
     */
    public function setInternalIsolationLayer(\Kraken\WarmBundle\Entity\Layer $internalIsolationLayer = null)
    {
        $this->internal_isolation_layer = $internalIsolationLayer;

        return $this;
    }

    /**
     * Get internal_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getInternalIsolationLayer()
    {
        return $this->internal_isolation_layer;
    }

    /**
     * Set external_isolation_layer
     *
     * @param \Kraken\WarmBundle\Entity\Layer $externalIsolationLayer
     * @return House
     */
    public function setExternalIsolationLayer(\Kraken\WarmBundle\Entity\Layer $externalIsolationLayer = null)
    {
        $this->external_isolation_layer = $externalIsolationLayer;

        return $this;
    }

    /**
     * Get external_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getExternalIsolationLayer()
    {
        return $this->external_isolation_layer;
    }

    /**
     * Set wall_size
     *
     * @param string $wallSize
     * @return House
     */
    public function setWallSize($wallSize)
    {
        $this->wall_size = $wallSize;

        return $this;
    }

    /**
     * Get wall_size
     *
     * @return string
     */
    public function getWallSize()
    {
        return $this->wall_size;
    }

    /**
     * Set number_balcony_doors
     *
     * @param integer $numberBalconyDoors
     * @return House
     */
    public function setNumberBalconyDoors($numberBalconyDoors)
    {
        $this->number_balcony_doors = $numberBalconyDoors;

        return $this;
    }

    /**
     * Get number_balcony_doors
     *
     * @return integer
     */
    public function getNumberBalconyDoors()
    {
        return $this->number_balcony_doors;
    }

    /**
     * Set number_huge_glazings
     *
     * @param integer $numberHugeGlazings
     * @return House
     */
    public function setNumberHugeGlazings($numberHugeGlazings)
    {
        $this->number_huge_glazings = $numberHugeGlazings;

        return $this;
    }

    /**
     * Get number_huge_glazings
     *
     * @return integer
     */
    public function getNumberHugeGlazings()
    {
        return $this->number_huge_glazings;
    }

    /**
     * Set top_isolation_layer
     *
     * @param \Kraken\WarmBundle\Entity\Layer $topIsolationLayer
     * @return House
     */
    public function setTopIsolationLayer(\Kraken\WarmBundle\Entity\Layer $topIsolationLayer = null)
    {
        $this->top_isolation_layer = $topIsolationLayer;

        return $this;
    }

    /**
     * Get top_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getTopIsolationLayer()
    {
        return $this->top_isolation_layer;
    }

    /**
     * Set bottom_isolation_layer
     *
     * @param \Kraken\WarmBundle\Entity\Layer $bottomIsolationLayer
     * @return House
     */
    public function setBottomIsolationLayer(\Kraken\WarmBundle\Entity\Layer $bottomIsolationLayer = null)
    {
        $this->bottom_isolation_layer = $bottomIsolationLayer;

        return $this;
    }

    /**
     * Get bottom_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getBottomIsolationLayer()
    {
        return $this->bottom_isolation_layer;
    }

    public function isRowHouseOnCorner()
    {
        return $this->is_row_house_on_corner;
    }

    public function setRowHouseOnCorner($onCorner)
    {
        $this->setRowHouseOnCorner($onCorner);

        return $this;
    }
}
