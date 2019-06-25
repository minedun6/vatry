<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Driver
 *
 * @ORM\Table(name="driver")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DriverRepository")
 */
class Driver
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;


    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255,nullable=true)
     */
    private $company;


    /**
     * @var string
     *
     * @ORM\Column(name="activity", type="string", length=255,nullable=true)
     */
    private $activity;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,unique=true)
     */
    private $email;


    /**
     * @var string
     *
     * @ORM\Column(name="vehicule", type="string", length=255)
     */
    private $vehicule;

    /**
     * @var string
     *
     * @ORM\Column(name="vehiculeCapacity", type="string", length=255)
     */
    private $vehiculeCapacity;

    /**
     * @var string
     *
     * @ORM\Column(name="vehiculeColor", type="string", length=255)
     */
    private $vehiculeColor;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true,options={"default":true})
     */
    private $status;


    /**
    * @ORM\OneToMany(targetEntity="Transfer", mappedBy="driver")
    */
    private $transfers;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Driver
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Driver
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Driver
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set vehiculeType
     *
     * @param string $vehiculeType
     *
     * @return Driver
     */
    public function setVehiculeType($vehiculeType)
    {
        $this->vehiculeType = $vehiculeType;

        return $this;
    }

    /**
     * Get vehiculeType
     *
     * @return string
     */
    public function getVehiculeType()
    {
        return $this->vehiculeType;
    }

    /**
     * Set vehiculeColor
     *
     * @param string $vehiculeColor
     *
     * @return Driver
     */
    public function setVehiculeColor($vehiculeColor)
    {
        $this->vehiculeColor = $vehiculeColor;

        return $this;
    }

    /**
     * Get vehiculeColor
     *
     * @return string
     */
    public function getVehiculeColor()
    {
        return $this->vehiculeColor;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return Driver
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set activity
     *
     * @param string $activity
     *
     * @return Driver
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Driver
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set vehicule
     *
     * @param string $vehicule
     *
     * @return Driver
     */
    public function setVehicule($vehicule)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return string
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set vehiculeCapacity
     *
     * @param string $vehiculeCapacity
     *
     * @return Driver
     */
    public function setVehiculeCapacity($vehiculeCapacity)
    {
        $this->vehiculeCapacity = $vehiculeCapacity;

        return $this;
    }

    /**
     * Get vehiculeCapacity
     *
     * @return string
     */
    public function getVehiculeCapacity()
    {
        return $this->vehiculeCapacity;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transfers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add transfer
     *
     * @param \AppBundle\Entity\Transfer $transfer
     *
     * @return Driver
     */
    public function addTransfer(\AppBundle\Entity\Transfer $transfer)
    {
        $this->transfers[] = $transfer;

        return $this;
    }

    /**
     * Remove transfer
     *
     * @param \AppBundle\Entity\Transfer $transfer
     */
    public function removeTransfer(\AppBundle\Entity\Transfer $transfer)
    {
        $this->transfers->removeElement($transfer);
    }

    /**
     * Get transfers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransfers()
    {
        return $this->transfers;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Driver
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }


    public function getStatusDesignation()
    {

      $stat=$this->getStatus();
      if($stat==true){
        return "Activé" ;
      }

      else {
        return "Desactivé" ;
      }
    }
}
