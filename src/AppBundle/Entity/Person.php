<?php

namespace AppBundle\Entity;

use AppBundle\Utilities\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Person
{
    use TimestampableTrait;

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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=50, nullable=true)
     */
    private $tel;

    /**
     * @var string
     * @ORM\Column(name="town",type="string",length=100,nullable=true)
     */
    private $town;

    /**
     * @var
     * @ORM\Column(name="zip_code",type="string",length=10,nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     * @ORM\Column(name="civility",type="string",length=3,nullable=true)
     */
    private $civility;

    /**
     * @var Country
     * @ORM\Column(name="country",type="string",length=10,nullable=true)
     */
    private $country;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transfer",mappedBy="createdBy")
     */
    private $transfers;

    /**
     * @var string
     * @ORM\Column(name="type",type="string",length=10,nullable=true)
     */
    private $type;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User",mappedBy="person", cascade={"persist"})
     */
    private $user;

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
     * Set email
     *
     * @param string $email
     * @return Person
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
     * Set name
     *
     * @param string $name
     * @return Person
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
     * @return Person
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
     * Set address
     *
     * @param string $address
     * @return Person
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return Person
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transfers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add transfers
     *
     * @param \AppBundle\Entity\Transfer $transfers
     * @return Person
     */
    public function addTransfer(\AppBundle\Entity\Transfer $transfers)
    {
        $this->transfers[] = $transfers;

        return $this;
    }

    /**
     * Remove transfers
     *
     * @param \AppBundle\Entity\Transfer $transfers
     */
    public function removeTransfer(\AppBundle\Entity\Transfer $transfers)
    {
        $this->transfers->removeElement($transfers);
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
     * Set town
     *
     * @param string $town
     * @return Person
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string 
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Person
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     * @return Person
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set civility
     *
     * @param string $civility
     * @return Person
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility
     *
     * @return string 
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Person
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

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Person
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

}
