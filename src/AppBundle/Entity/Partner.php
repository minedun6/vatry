<?php

namespace AppBundle\Entity;

use AppBundle\Utilities\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Partner
 *
 * @ORM\Table(name="partner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PartnerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Partner
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var SourceToken
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SourceToken",mappedBy="partner")
     */
    private $tokens;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transfer",mappedBy="partner")
     */
    private $transfers;

    /**
     * @var User
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User",mappedBy="partner",cascade={"persist"})
     */
    private $users;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_airport", type="boolean", nullable=true)
     */
    private $isAirport;

    /**
     * @var string
     *
     * @ORM\Column(name="raisonSociale", type="string", length=255, nullable=true)
     */
    private $raisonSociale;

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
     * Set name
     *
     * @param string $name
     * @return Partner
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
     * Constructor
     */
    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

    /**
     * Add tokens
     *
     * @param \AppBundle\Entity\SourceToken $tokens
     * @return Partner
     */
    public function addToken(\AppBundle\Entity\SourceToken $tokens)
    {
        $this->tokens[] = $tokens;

        return $this;
    }

    /**
     * Remove tokens
     *
     * @param \AppBundle\Entity\SourceToken $tokens
     */
    public function removeToken(\AppBundle\Entity\SourceToken $tokens)
    {
        $this->tokens->removeElement($tokens);
    }

    /**
     * Get tokens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Add transfers
     *
     * @param \AppBundle\Entity\Transfer $transfers
     * @return Partner
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
     * Add users
     *
     * @param \AppBundle\Entity\User $user
     * @return Partner
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $user->setPartner($this);
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return User
     */
    public function getPrincipalUser()
    {
        return $this->users[0];
    }

    /**
     * Set isAirport
     *
     * @param boolean $isAirport
     *
     * @return Partner
     */
    public function setIsAirport($isAirport)
    {
        $this->isAirport = $isAirport;

        return $this;
    }

    /**
     * Get isAirport
     *
     * @return boolean
     */
    public function getIsAirport()
    {
        return $this->isAirport;
    }

    /**
     * Set raisonSociale
     *
     * @param string $raisonSociale
     * @return Partner
     */
    public function setRaisonSociale($raisonSociale)
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    /**
     * Get raisonSociale
     *
     * @return string 
     */
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }
}
