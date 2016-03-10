<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="double_house")
 */
class DoubleHouse
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=3)
     * @Assert\Range(min="1", minMessage="Porządny dom powinien mieć min. 1m szerokości", max="100", maxMessage = "Powyżej 100m szerokości to już hangar, a nie dom mieszkalny")
     */
    protected $building_length;

    /**
     * @ORM\Column(type="integer", length=3)
     * @Assert\Range(min="1", minMessage = "Porządny dom powinien mieć min. 1m długości", max="100", maxMessage = "Powyżej 100m długości to już hangar, a nie dom mieszkalny")
     */
    protected $building_width;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\Range(min="1", minMessage = "Nawet ziemianka ma min. 1 piętro", max="100", maxMessage = "Nie wierzę, że masz więcej niż 100 pięter w swojej chałupie")
     */
    protected $number_floors;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\Range(min="0", minMessage = "Za mało drzwi zewnętrznych", max="99", maxMessage = "Więcej jak 99 drzwi nie jest ci potrzebne. Sprzedaj połowę.")
     */
    protected $number_doors;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('old_wooden', 'old_metal', 'new_wooden', 'new_metal', 'other')")
     */
    protected $doors_type;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\Range(min="1", minMessage = "Min. 1 okno powinieneś posiadać", max="99", maxMessage = "Więcej jak 99 okien nie jest ci potrzebne. Sprzedaj połowę.")
     */
    protected $number_windows;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('old_single_glass', 'old_double_glass', 'old_improved', 'semi_new_double_glass', 'new_double_glass', 'new_triple_glass')")
     */
    protected $windows_type;

    /**
     * @ORM\OneToMany(targetEntity="Wall", mappedBy="house", cascade={"all"})
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
     * @ORM\Column(type="boolean")
     */
    protected $is_attic_heated;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $has_balcony;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $has_basement;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_basement_heated;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_ground_floor_heated;

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
     * @ORM\Column(type="boolean")
     */
    protected $has_garage;

    /**
     * @ORM\OneToMany(targetEntity="Calculation", mappedBy="house", cascade={"all"})
     */
    protected $calculations;

    public static function create()
    {
        $house = new House();

        return $house;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->walls = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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

    /**
     * Set number_doors.
     *
     * @param int $numberDoors
     *
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * Set is_attic_heated.
     *
     * @param bool $isAtticHeated
     *
     * @return DoubleHouse
     */
    public function setIsAtticHeated($isAtticHeated)
    {
        $this->is_attic_heated = $isAtticHeated;

        return $this;
    }

    /**
     * Get is_attic_heated.
     *
     * @return bool
     */
    public function getIsAtticHeated()
    {
        return $this->is_attic_heated;
    }

    /**
     * Set has_basement.
     *
     * @param bool $hasBasement
     *
     * @return DoubleHouse
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
     * Set is_basement_heated.
     *
     * @param bool $isBasementHeated
     *
     * @return DoubleHouse
     */
    public function setIsBasementHeated($isBasementHeated)
    {
        $this->is_basement_heated = $isBasementHeated;

        return $this;
    }

    /**
     * Get is_basement_heated.
     *
     * @return bool
     */
    public function getIsBasementHeated()
    {
        return $this->is_basement_heated;
    }

    /**
     * Set is_ground_floor_heated.
     *
     * @param bool $isGroundFloorHeated
     *
     * @return DoubleHouse
     */
    public function setIsGroundFloorHeated($isGroundFloorHeated)
    {
        $this->is_ground_floor_heated = $isGroundFloorHeated;

        return $this;
    }

    /**
     * Get is_ground_floor_heated.
     *
     * @return bool
     */
    public function getIsGroundFloorHeated()
    {
        return $this->is_ground_floor_heated;
    }

    /**
     * Set ventilation_type.
     *
     * @param string $ventilationType
     *
     * @return DoubleHouse
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
     * @return DoubleHouse
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
    public function hasGarage()
    {
        return $this->has_garage;
    }

    /**
     * Add walls.
     *
     * @param \Kraken\WarmBundle\Entity\Wall $walls
     *
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
     * @return DoubleHouse
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
}
