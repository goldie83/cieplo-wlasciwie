<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="calculation")
 */
class Calculation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", scale=7)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="decimal", scale=7)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('single_house', 'double_house', 'row_house', 'apartment')")
     */
    protected $building_type;

    /**
     * @ORM\Column(type="integer", length=4)
     * @Assert\Range(min="1900", minMessage="Jeśli dom jest sprzed XX wieku, wybierz 1900 rok")
     */
    protected $construction_year;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min="10", minMessage = "To zbyt niska temperatura dla budynku mieszkalnego", max="50", maxMessage = "To zbyt wysoka temperatura dla budynku mieszkalnego", invalidMessage="Nieprawidłowa wartość")
     */
    protected $indoor_temperature;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @deprecated
     */
    protected $fuel_type;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @deprecated
     */
    protected $stove_type;

    /**
     * @ORM\ManyToOne(targetEntity="HeatingDevice", inversedBy="calculations", cascade={"persist"})
     * @ORM\JoinColumn(name="heating_device_id", referencedColumnName="id", nullable=true)
     */
    protected $heating_device;

    /**
     * @ORM\OneToMany(targetEntity="FuelConsumption", mappedBy="calculation", cascade={"all"})
     */
    protected $fuel_consumptions;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(min="0.01", minMessage = "Nie za mało?")
     */
    protected $stove_power;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="House", inversedBy="calculations",cascade={"all"})
     */
    protected $house;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $heated_area;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $heating_power;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="calculations", cascade={"persist"})
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    protected $city;

    public static function create()
    {
        $calc = new Calculation();
        $calc->setLatitude(51.917168);
        $calc->setLongitude(19.138184);

        return $calc;
    }

    public function isFuelConsumptionProvided()
    {
        return count($this->getFuelConsumptions()) > 0;
    }

    public function getFuelLabel()
    {
        $labels = [];

        foreach ($this->getFuelConsumptions() as $fc) {
            $amount = round($fc->getConsumption(), 1);
            $labels[] = $fc->getFuel()->getName() . ' ' . $amount . $fc->getFuel()->getTradeUnit();
        }

        return implode(', ', $labels);
    }

    public function getFuelCost()
    {
        $cost = 0;

        foreach ($this->getFuelConsumptions() as $fc) {
            $cost += $fc->getCost();
        }

        return $cost;
    }

    public function getLabel()
    {
        $types = array(
            'single_house' => 'Budynek jednorodzinny',
            'double_house' => 'Bliźniak',
            'row_house' => 'Dom w zabudowie szeregowej',
            'apartment' => 'Mieszkanie',
        );

        $house = $this->getHouse();
        $l = $house->getBuildingLength();
        $w = $house->getBuildingWidth();
        $floors = $house->getNumberFloors();

        return $types[$this->building_type] . ', ' . round($w * $l * $floors) . 'm2';
    }

    public function getSlug()
    {
        return base_convert($this->getId(), 10, 36);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set construction_year
     *
     * @param  integer     $constructionYear
     * @return Calculation
     */
    public function setConstructionYear($constructionYear)
    {
        $this->construction_year = $constructionYear;

        return $this;
    }

    /**
     * Get construction_year
     *
     * @return integer
     */
    public function getConstructionYear()
    {
        return $this->construction_year;
    }

    /**
     * Set latitude
     *
     * @param  float       $latitude
     * @return Calculation
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param  float       $longitude
     * @return Calculation
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set created
     *
     * @param  \DateTime   $created
     * @return Calculation
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime   $updated
     * @return Calculation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set building_type
     *
     * @param  string      $buildingType
     * @return Calculation
     */
    public function setBuildingType($buildingType)
    {
        $this->building_type = $buildingType;

        return $this;
    }

    /**
     * Get building_type
     *
     * @return string
     */
    public function getBuildingType()
    {
        return $this->building_type;
    }

    /**
     * Set indoor_temperature
     *
     * @param  float       $indoorTemperature
     * @return Calculation
     */
    public function setIndoorTemperature($indoorTemperature)
    {
        $this->indoor_temperature = $indoorTemperature;

        return $this;
    }

    /**
     * Get indoor_temperature
     *
     * @return float
     */
    public function getIndoorTemperature()
    {
        return $this->indoor_temperature;
    }

    /**
     * Set fuel_type
     *
     * @param  string      $fuelType
     * @return Calculation
     */
    public function setFuelType($fuelType)
    {
        $this->fuel_type = $fuelType;

        return $this;
    }

    /**
     * Get fuel_type
     *
     * @return string
     */
    public function getFuelType()
    {
        return $this->fuel_type;
    }

    public function setStoveType($stoveType)
    {
        $this->stove_type = $stoveType;

        return $this;
    }

    /**
     * Get stove_type
     *
     * @return string
     */
    public function getStoveType()
    {
        return $this->stove_type;
    }

    /**
     * Set house
     *
     * @param  \Kraken\WarmBundle\Entity\House $house
     * @return Calculation
     */
    public function setHouse(\Kraken\WarmBundle\Entity\House $house = null)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return \Kraken\WarmBundle\Entity\House
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set stove_power
     *
     * @param  float       $stovePower
     * @return Calculation
     */
    public function setStovePower($stovePower)
    {
        $this->stove_power = $stovePower;

        return $this;
    }

    /**
     * Get stove_power
     *
     * @return float
     */
    public function getStovePower()
    {
        return $this->stove_power;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isUsingSolidFuel()
    {
        return !stristr($this->getFuelType(), 'electricity') && !stristr($this->getFuelType(), 'gas');
    }

    /**
     * Set heated_area
     *
     * @param string $heatedArea
     * @return Calculation
     */
    public function setHeatedArea($heatedArea)
    {
        $this->heated_area = $heatedArea;

        return $this;
    }

    /**
     * Get heated_area
     *
     * @return string 
     */
    public function getHeatedArea()
    {
        return $this->heated_area;
    }

    /**
     * Set heating_power
     *
     * @param string $heatingPower
     * @return Calculation
     */
    public function setHeatingPower($heatingPower)
    {
        $this->heating_power = $heatingPower;

        return $this;
    }

    /**
     * Get heating_power
     *
     * @return string 
     */
    public function getHeatingPower()
    {
        return $this->heating_power;
    }

    /**
     * Set city
     *
     * @param \Kraken\WarmBundle\Entity\City $city
     * @return Calculation
     */
    public function setCity(\Kraken\WarmBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Kraken\WarmBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set fuel
     *
     * @param \Kraken\WarmBundle\Entity\Fuel $fuel
     * @return Calculation
     */
    public function setFuel(\Kraken\WarmBundle\Entity\Fuel $fuel = null)
    {
        $this->fuel = $fuel;

        return $this;
    }

    /**
     * Get fuel
     *
     * @return \Kraken\WarmBundle\Entity\Fuel 
     */
    public function getFuel()
    {
        return $this->fuel;
    }

    /**
     * Set heating_device
     *
     * @param \Kraken\WarmBundle\Entity\HeatingDevice $heatingDevice
     * @return Calculation
     */
    public function setHeatingDevice(\Kraken\WarmBundle\Entity\HeatingDevice $heatingDevice = null)
    {
        $this->heating_device = $heatingDevice;

        return $this;
    }

    /**
     * Get heating_device
     *
     * @return \Kraken\WarmBundle\Entity\HeatingDevice 
     */
    public function getHeatingDevice()
    {
        return $this->heating_device;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fuel_consumptions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fuel_consumptions
     *
     * @param \Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions
     * @return Calculation
     */
    public function addFuelConsumption(\Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions)
    {
        $this->fuel_consumptions[] = $fuelConsumptions;

        return $this;
    }

    /**
     * Remove fuel_consumptions
     *
     * @param \Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions
     */
    public function removeFuelConsumption(\Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions)
    {
        $this->fuel_consumptions->removeElement($fuelConsumptions);
    }

    /**
     * Get fuel_consumptions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFuelConsumptions()
    {
        return $this->fuel_consumptions;
    }
}
