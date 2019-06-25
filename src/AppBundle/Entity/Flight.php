<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flight
 *
 * @ORM\Table(name="flight")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FlightRepository")
 */
class Flight
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
     * @ORM\Column(name="num", type="string", length=20)
     */
    private $num;

    /**
     * @var string
     *
     * @ORM\Column(name="fromLocation", type="string", length=100, nullable=true)
     */
    private $fromLocation;

    /**
     * @var string
     *
     * @ORM\Column(name="toLocation", type="string", length=100)
     */
    private $toLocation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string
     * @ORM\Column(name="country",type="string",length=50,nullable=true)
     */
    private $country;

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
     * Set num
     *
     * @param string $num
     * @return Flight
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set fromLocation
     *
     * @param string $fromLocation
     * @return Flight
     */
    public function setFromLocation($fromLocation)
    {
        $this->fromLocation = $fromLocation;

        return $this;
    }

    /**
     * Get fromLocation
     *
     * @return string 
     */
    public function getFromLocation()
    {
        return $this->fromLocation;
    }

    /**
     * Set toLocation
     *
     * @param string $toLocation
     * @return Flight
     */
    public function setToLocation($toLocation)
    {
        $this->toLocation = $toLocation;

        return $this;
    }

    /**
     * Get toLocation
     *
     * @return string 
     */
    public function getToLocation()
    {
        return $this->toLocation;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Flight
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    public function getProvDistLabel(){
        if ($this->fromLocation == 'vatry'){
            return 'Déstination';
        }else{
            return 'Provenance';
        }
    }

    public function getVolDir(){
        if ($this->fromLocation == 'vatry'){
            return $this->toLocation ;
        }else{
            return $this->fromLocation;
        }
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Flight
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }
}
