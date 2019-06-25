<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrivateLocationPrice
 *
 * @ORM\Table(name="private_location_prices")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivateLocationPriceRepository")
 */
class PrivateLocationPrice
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
     * @var float
     *
     * @ORM\Column(name="price", type="float" , nullable=true)
     */
    private $price;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     */
    private $location;

    /**
     * @var int
     * @ORM\Column(name="min_capacity",type="integer",nullable=true)
     */
    private $minCapacity;

    /**
     * @var int
     * @ORM\Column(name="max_capacity",type="integer",nullable=true)
     */
    private $maxCapacity;

    /**
     * @var integer
     * @ORM\Column(name="distance",type="integer",nullable=true)
     */
    private $distance;

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
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     */
    private $zipCode;


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
     * Set minCapacity
     *
     * @param integer $minCapacity
     * @return PrivateLocationPrice
     */
    public function setMinCapacity($minCapacity)
    {
        $this->minCapacity = $minCapacity;

        return $this;
    }

    /**
     * Get minCapacity
     *
     * @return integer 
     */
    public function getMinCapacity()
    {
        return $this->minCapacity;
    }

    /**
     * Set maxCapacity
     *
     * @param integer $maxCapacity
     * @return PrivateLocationPrice
     */
    public function setMaxCapacity($maxCapacity)
    {
        $this->maxCapacity = $maxCapacity;

        return $this;
    }

    /**
     * Get maxCapacity
     *
     * @return integer 
     */
    public function getMaxCapacity()
    {
        return $this->maxCapacity;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     * @return PrivateLocationPrice
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
     * Set price
     *
     * @param float $price
     * @return PrivateLocationPrice
     */
    public function setPrice($price)
    {
        $price = strval($price);
        $price = str_replace(',','.',$price);
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     * @return PrivateLocationPrice
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer 
     */
    public function getDistance()
    {
        return $this->distance;
    }
}
