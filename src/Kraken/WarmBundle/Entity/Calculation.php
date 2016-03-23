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
     * @ORM\Column(type="integer", length=4, nullable=true)
     * @Assert\Range(min="1900", minMessage="Jeśli dom jest sprzed XX wieku, wybierz 1900 rok")
     */
    protected $construction_year;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     * @Assert\Range(min="10", minMessage = "To zbyt niska temperatura dla budynku mieszkalnego", max="50", maxMessage = "To zbyt wysoka temperatura dla budynku mieszkalnego", invalidMessage="Nieprawidłowa wartość")
     */
    protected $indoor_temperature;

    /**
     * @ORM\ManyToOne(targetEntity="HeatingDevice", inversedBy="calculations", cascade={"persist"})
     * @ORM\JoinColumn(name="heating_device_id", referencedColumnName="id", nullable=true)
     */
    protected $heating_device;

    /**
     * @ORM\OneToMany(targetEntity="FuelConsumption", mappedBy="calculation")
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $custom_data;

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

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $include_hot_water;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hot_water_persons;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hot_water_use;

    public static $constructionYears = [
        2011 => 'lata 2011 – 2016',
        2000 => 'lata 2000 – 2010',
        1990 => 'lata 90-te',
        1980 => 'lata 80-te',
        1970 => 'lata 70-te',
        1960 => 'lata 60-te',
        1950 => 'lata 50-te',
        1940 => 'lata 40-te',
        1939 => 'gdzieś przed II wojną',
        1914 => 'gdzieś przed I wojną',
    ];

    public static function create()
    {
        $calc = new self();
        $calc->setLatitude(51.917168);
        $calc->setLongitude(19.138184);

        return $calc;
    }

    public function isFuelConsumptionProvided()
    {
        foreach ($this->getFuelConsumptions() as $fc) {
            if ($fc->getFuel() != null && $fc->getConsumption() > 0) {
                return true;
            }
        }

        return false;
    }

    public function getFuelCost()
    {
        $cost = 0;

        foreach ($this->getFuelConsumptions() as $fc) {
            if ($fc->getFuel()) {
                $cost += $fc->getCost();
            }
        }

        return $cost;
    }

    public function getStoveType()
    {
        return $this->getHeatingDevice() ? $this->getHeatingDevice()->getType() : '';
    }

    public function getFuelType()
    {
        $consumption = $this->getFuelConsumptions();

        return count($consumption) > 0 && $consumption->get(0)->getFuel() != null ? $consumption->get(0)->getFuel()->getType() : '';
    }

    public function getSlug()
    {
        return base_convert($this->getId(), 10, 36);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setConstructionYear($constructionYear)
    {
        $this->construction_year = $constructionYear;

        return $this;
    }

    public function getConstructionYear()
    {
        return $this->construction_year;
    }

    public function getConstructionPeriod()
    {
        return self::$constructionYears[$this->construction_year];
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setBuildingType($buildingType)
    {
        $this->building_type = $buildingType;

        return $this;
    }

    public function getBuildingType()
    {
        return $this->building_type;
    }

    public function isApartment()
    {
        return $this->building_type == 'apartment';
    }

    public function setIndoorTemperature($indoorTemperature)
    {
        $this->indoor_temperature = $indoorTemperature;

        return $this;
    }

    public function getIndoorTemperature()
    {
        return $this->indoor_temperature;
    }

    public function setHouse(\Kraken\WarmBundle\Entity\House $house = null)
    {
        $this->house = $house;

        return $this;
    }

    public function getHouse()
    {
        return $this->house ?: House::create();
    }

    public function setStovePower($stovePower)
    {
        $this->stove_power = $stovePower;

        return $this;
    }

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
        foreach ($this->fuel_consumptions as $fc) {
            $type = $fc->getFuel() ? $fc->getFuel()->getType() : '';
            if (stristr($type, 'coke') || stristr($type, 'coal') || stristr($type, 'wood') || stristr($type, 'pellet')) {
                return true;
            }
        }

        return false;
    }

    public function setHeatedArea($heatedArea)
    {
        $this->heated_area = $heatedArea;

        return $this;
    }

    public function getHeatedArea()
    {
        return $this->heated_area;
    }

    public function setHeatingPower($heatingPower)
    {
        $this->heating_power = $heatingPower;

        return $this;
    }

    public function getHeatingPower()
    {
        return $this->heating_power;
    }

    /**
     * Set city.
     *
     * @param \Kraken\WarmBundle\Entity\City $city
     *
     * @return Calculation
     */
    public function setCity(\Kraken\WarmBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return \Kraken\WarmBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set heating_device.
     *
     * @param \Kraken\WarmBundle\Entity\HeatingDevice $heatingDevice
     *
     * @return Calculation
     */
    public function setHeatingDevice(\Kraken\WarmBundle\Entity\HeatingDevice $heatingDevice = null)
    {
        $this->heating_device = $heatingDevice;

        return $this;
    }

    /**
     * Get heating_device.
     *
     * @return \Kraken\WarmBundle\Entity\HeatingDevice
     */
    public function getHeatingDevice()
    {
        return $this->heating_device;
    }

    public function __construct()
    {
        $this->fuel_consumptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->house = House::create();
    }

    /**
     * Add fuel_consumptions.
     *
     * @param \Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions
     *
     * @return Calculation
     */
    public function addFuelConsumption($fuelConsumptions)
    {
        if ($fuelConsumptions instanceof \Kraken\WarmBundle\Entity\fuelConsumption) {
            $this->fuel_consumptions[] = $fuelConsumptions;
        }

        return $this;
    }

    /**
     * Remove fuel_consumptions.
     *
     * @param \Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions
     */
    public function removeFuelConsumption(\Kraken\WarmBundle\Entity\fuelConsumption $fuelConsumptions)
    {
        $this->fuel_consumptions->removeElement($fuelConsumptions);
    }

    /**
     * Get fuel_consumptions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFuelConsumptions()
    {
        return $this->fuel_consumptions;
    }

    /**
     * Set custom_data.
     *
     * @param string $customData
     *
     * @return Calculation
     */
    public function setCustomData($customData)
    {
        $this->custom_data = $customData;

        return $this;
    }

    /**
     * Get custom_data.
     *
     * @return string
     */
    public function getCustomData()
    {
        return $this->custom_data;
    }

    /**
     * Set include_hot_water.
     *
     * @param bool $includeHotWater
     *
     * @return Calculation
     */
    public function setIncludeHotWater($includeHotWater)
    {
        $this->include_hot_water = $includeHotWater;

        return $this;
    }

    /**
     * Get include_hot_water.
     *
     * @return bool
     */
    public function getIncludeHotWater()
    {
        return $this->include_hot_water;
    }

    /**
     * Set hot_water_persons.
     *
     * @param int $hotWaterPersons
     *
     * @return Calculation
     */
    public function setHotWaterPersons($hotWaterPersons)
    {
        $this->hot_water_persons = $hotWaterPersons;

        return $this;
    }

    /**
     * Get hot_water_persons.
     *
     * @return int
     */
    public function getHotWaterPersons()
    {
        return $this->hot_water_persons;
    }

    /**
     * Set hot_water_use.
     *
     * @param int $hotWaterUse
     *
     * @return Calculation
     */
    public function setHotWaterUse($hotWaterUse)
    {
        $this->hot_water_use = $hotWaterUse;

        return $this;
    }

    /**
     * Get hot_water_use.
     *
     * @return int
     */
    public function getHotWaterUse()
    {
        return $this->hot_water_use;
    }
}
