<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PorteAPortePrice
 *
 * @ORM\Table(name="porte_a_porte_price")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PorteAPortePriceRepository")
 */
class PorteAPortePrice
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
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     */
    private $zipCode;


    /**
     * @var int
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;      //refers to adult price,was not modified to prevent errors

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
     * @ORM\Column(name="agencyprice", type="float")
     */
    private $agencyprice;  //refers to adult price


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
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     */
    private $location;


    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Agglomeration")
     */
    private $agglomeration;

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
     * Set price
     *
     * @param integer $price
     * @return PorteAPortePrice
     */
    public function setPrice($price)
    {
        $this->price = $price;

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
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;

    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     * @return PorteAPortePrice
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

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
     * Set agglomeration
     *
     * @param \AppBundle\Entity\Agglomeration $agglomeration
     * @return PorteAPortePrice
     */
    public function setAgglomeration(\AppBundle\Entity\Agglomeration $agglomeration = null)
    {
        $this->agglomeration = $agglomeration;

        return $this;
    }

    /**
     * Get agglomeration
     *
     * @return \AppBundle\Entity\Agglomeration
     */
    public function getAgglomeration()
    {
        return $this->agglomeration;
    }

    /**
     * Set agencyprice
     *
     * @param float $agencyprice
     *
     * @return PorteAPortePrice
     */
    public function setAgencyprice($agencyprice)
    {
        $this->agencyprice = $agencyprice;

        return $this;
    }

    /**
     * Get agencyprice
     *
     * @return float
     */
    public function getAgencyprice()
    {
        return $this->agencyprice;
    }

    /**
     * Set twoadultsPrice
     *
     * @param float $twoadultsPrice
     *
     * @return PorteAPortePrice
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
     * @return PorteAPortePrice
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
     * Set childPrice
     *
     * @param float $childPrice
     *
     * @return PorteAPortePrice
     */
    public function setChildPrice($childPrice)
    {
        $this->childPrice = $childPrice;

        return $this;
    }

    /**
     * Get childPrice
     *
     * @return float
     */
    public function getChildPrice()
    {
        return $this->childPrice;
    }

    /**
     * Set babyPrice
     *
     * @param float $babyPrice
     *
     * @return PorteAPortePrice
     */
    public function setBabyPrice($babyPrice)
    {
        $this->babyPrice = $babyPrice;

        return $this;
    }

    /**
     * Get babyPrice
     *
     * @return float
     */
    public function getBabyPrice()
    {
        return $this->babyPrice;
    }

    /**
     * Set agencytwoadultsPrice
     *
     * @param float $agencytwoadultsPrice
     *
     * @return PorteAPortePrice
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
     * @return PorteAPortePrice
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
     * @return PorteAPortePrice
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
     * @return PorteAPortePrice
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
