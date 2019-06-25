<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterVillePrices
 *
 * @ORM\Table(name="inter_ville_prices")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterVillePricesRepository")
 */
class InterVillePrices
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="rdv", type="string", length=100, nullable=true)
     */
    private $rdv;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     */
    private $zipCode;


    /**
     * @var int
     *
     * @ORM\Column(name="adultePrice", type="float", nullable=true)
     */
    private $adultePrice;


    /**
     * @var int
     *
     * @ORM\Column(name="twoadultsPrice", type="float", nullable=true)
     */
    private $twoadultsPrice;


    /**
     * @var int
     *
     * @ORM\Column(name="threeadultsPrice", type="float", nullable=true)
     */
    private $threeadultsPrice;


    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     */
    private $location;

    /**
     * @var int
     *
     * @ORM\Column(name="childPrice", type="float", nullable=true)
     */
    private $childPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="babyPrice", type="float", nullable=true)
     */
    private $babyPrice;



    /**
     * @var int
     *
     * @ORM\Column(name="agencyadultePrice", type="float", nullable=true)
     */
    private $agencyadultePrice;


    /**
     * @var int
     *
     * @ORM\Column(name="agencytwoadultsPrice", type="float", nullable=true)
     */
    private $agencytwoadultsPrice;


    /**
     * @var int
     *
     * @ORM\Column(name="agencythreeadultsPrice", type="float", nullable=true)
     */
    private $agencythreeadultsPrice;



    /**
     * @var int
     *
     * @ORM\Column(name="agencychildPrice", type="float", nullable=true)
     */
    private $agencychildPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="agencybabyPrice", type="float", nullable=true)
     */
    private $agencybabyPrice;



    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\City")
     */
    private $cities;

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
     * Set adultePrice
     *
     * @param integer $adultePrice
     * @return InterVillePrices
     */
    public function setAdultePrice($adultePrice)
    {
        $this->adultePrice = $adultePrice;

        return $this;
    }

    /**
     * Get adultePrice
     *
     * @return integer
     */
    public function getAdultePrice()
    {
        return $this->adultePrice;
    }

    /**
     * Set childPrice
     *
     * @param integer $childPrice
     * @return InterVillePrices
     */
    public function setChildPrice($childPrice)
    {
        $this->childPrice = $childPrice;

        return $this;
    }

    /**
     * Get childPrice
     *
     * @return integer
     */
    public function getChildPrice()
    {
        return $this->childPrice;
    }

    /**
     * Set babyPrice
     *
     * @param integer $babyPrice
     * @return InterVillePrices
     */
    public function setBabyPrice($babyPrice)
    {
        $this->babyPrice = $babyPrice;

        return $this;
    }

    /**
     * Get babyPrice
     *
     * @return integer
     */
    public function getBabyPrice()
    {
        return $this->babyPrice;
    }

    /**
     * Set rdv
     *
     * @param string $rdv
     * @return InterVillePrices
     */
    public function setRdv($rdv)
    {
        $this->rdv = $rdv;

        return $this;
    }

    /**
     * Get rdv
     *
     * @return string
     */
    public function getRdv()
    {
        return $this->rdv;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     * @return InterVillePrices
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return InterVillePrices
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cities
     *
     * @param \AppBundle\Entity\City $cities
     * @return InterVillePrices
     */
    public function addCity(\AppBundle\Entity\City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param \AppBundle\Entity\City $cities
     */
    public function removeCity(\AppBundle\Entity\City $cities)
    {
        $this->cities->removeElement($cities);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }

    public function getCitiesTable(){
        $locations=[];
        foreach ($this->cities as $c){
            $locations[]=$c->getDesignation();
        }
        return $locations;
    }



    /**
     * Set twoadultsPrice
     *
     * @param float $twoadultsPrice
     *
     * @return InterVillePrices
     */
    public function setTwoadultsPrice($twoadultsPrice)
    {
        $this->twoadultsPrice = $twoadultsPrice;

        return $this;
    }

    /**
     * Get twoadultsPrice
     *
     * @return float
     */
    public function getTwoadultsPrice()
    {
        return $this->twoadultsPrice;
    }

    /**
     * Set threeadultsPrice
     *
     * @param float $threeadultsPrice
     *
     * @return InterVillePrices
     */
    public function setThreeadultsPrice($threeadultsPrice)
    {
        $this->threeadultsPrice = $threeadultsPrice;

        return $this;
    }

    /**
     * Get threeadultsPrice
     *
     * @return float
     */
    public function getThreeadultsPrice()
    {
        return $this->threeadultsPrice;
    }

    /**
     * Set agencyadultePrice
     *
     * @param float $agencyadultePrice
     *
     * @return InterVillePrices
     */
    public function setAgencyadultePrice($agencyadultePrice)
    {
        $this->agencyadultePrice = $agencyadultePrice;

        return $this;
    }

    /**
     * Get agencyadultePrice
     *
     * @return float
     */
    public function getAgencyadultePrice()
    {
        return $this->agencyadultePrice;
    }

    /**
     * Set agencytwoadultsPrice
     *
     * @param float $agencytwoadultsPrice
     *
     * @return InterVillePrices
     */
    public function setAgencytwoadultsPrice($agencytwoadultsPrice)
    {
        $this->agencytwoadultsPrice = $agencytwoadultsPrice;

        return $this;
    }

    /**
     * Get agencytwoadultsPrice
     *
     * @return float
     */
    public function getAgencytwoadultsPrice()
    {
        return $this->agencytwoadultsPrice;
    }

    /**
     * Set agencythreeadultsPrice
     *
     * @param float $agencythreeadultsPrice
     *
     * @return InterVillePrices
     */
    public function setAgencythreeadultsPrice($agencythreeadultsPrice)
    {
        $this->agencythreeadultsPrice = $agencythreeadultsPrice;

        return $this;
    }

    /**
     * Get agencythreeadultsPrice
     *
     * @return float
     */
    public function getAgencythreeadultsPrice()
    {
        return $this->agencythreeadultsPrice;
    }

    /**
     * Set agencychildPrice
     *
     * @param float $agencychildPrice
     *
     * @return InterVillePrices
     */
    public function setAgencychildPrice($agencychildPrice)
    {
        $this->agencychildPrice = $agencychildPrice;

        return $this;
    }

    /**
     * Get agencychildPrice
     *
     * @return float
     */
    public function getAgencychildPrice()
    {
        return $this->agencychildPrice;
    }

    /**
     * Set agencybabyPrice
     *
     * @param float $agencybabyPrice
     *
     * @return InterVillePrices
     */
    public function setAgencybabyPrice($agencybabyPrice)
    {
        $this->agencybabyPrice = $agencybabyPrice;

        return $this;
    }

    /**
     * Get agencybabyPrice
     *
     * @return float
     */
    public function getAgencybabyPrice()
    {
        return $this->agencybabyPrice;
    }
}
