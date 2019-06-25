<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AgencePartner
 *
 * @ORM\Table(name="agence_partner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgencePartnerRepository")
 */
class AgencePartner
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
     * @ORM\Column(name="tel", type="string", length=50, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="nbjours", type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min = 0,
     *      max = 31,
     *      minMessage = "Nombre minimum est {{ limit }}",
     *      maxMessage = "Nombre maximum est {{ limit }}"
     * )
     */
    private $nbjours;

    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="text", nullable=true)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     */
    private $observations;

    /**
     * @var string
     *
     * @ORM\Column(name="referent", type="string", length=255, nullable=true)
     */
    private $referent;

    /**
     * @var string
     *
     * @ORM\Column(name="posteRefernet", type="string", length=255, nullable=true)
     */
    private $posteRefernet;

    /**
     * @var string
     *
     * @ORM\Column(name="rc", type="string", length=255, nullable=true)
     */
    private $rc;

    /**
     * @var string
     *
     * @ORM\Column(name="activity", type="string", length=100, nullable=true)
     */
    private $activity;

    /**
     * @var int
     *
     * @ORM\Column(name="commission", type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min = 0,
     *      max = 100,
     *      minMessage = "Commission minimum est {{ limit }}",
     *      maxMessage = "Commission maximum est {{ limit }}"
     * )
     */
    private $commission;

    /**
     * @var AgenceTransfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transfer",mappedBy="affectedTo")
     */
    private $agenceTransfers;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User",mappedBy="agencePartner", cascade={"persist"})
     */
    private $user;

    /**
     * @var Agence
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Agence", inversedBy="agencePartner")
     * @ORM\JoinColumn(name="agence_id", referencedColumnName="id")
     */
    private $agence;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="agences")
     */
    private $createdBy;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\B2BInvoice",mappedBy="partnerAgency")
     */
    private $b2bEnvoices;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MonthlyInvoice",mappedBy="partnerAgency")
     */
    private $monthlyB2bEnvoices;


    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", length=255,options={"default":true})
     */
    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="isPrePayment", type="boolean", options={"default":false})
     */
    private $isPrePayment;

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
     * Set tel
     *
     * @param string $tel
     * @return AgencePartner
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
     * Set email
     *
     * @param string $email
     * @return AgencePartner
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
     * Set nbjours
     *
     * @param integer $nbjours
     * @return AgencePartner
     */
    public function setNbjours($nbjours)
    {
        $this->nbjours = $nbjours;

        return $this;
    }

    /**
     * Get nbjours
     *
     * @return integer
     */
    public function getNbjours()
    {
        return $this->nbjours;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return AgencePartner
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set observations
     *
     * @param string $observations
     * @return AgencePartner
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * Get observations
     *
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Set referent
     *
     * @param string $referent
     * @return AgencePartner
     */
    public function setReferent($referent)
    {
        $this->referent = $referent;

        return $this;
    }

    /**
     * Get referent
     *
     * @return string
     */
    public function getReferent()
    {
        return $this->referent;
    }

    /**
     * Set activity
     *
     * @param string $activity
     * @return AgencePartner
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
     * Set commission
     *
     * @param integer $commission
     * @return AgencePartner
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return integer
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set agence
     *
     * @param \AppBundle\Entity\Agence $agence
     * @return AgencePartner
     */
    public function setAgence(\AppBundle\Entity\Agence $agence = null)
    {
        $this->agence = $agence;

        return $this;
    }

    /**
     * Get agence
     *
     * @return \AppBundle\Entity\Agence
     */
    public function getAgence()
    {
        return $this->agence;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agenceTransfers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add agenceTransfers
     *
     * @param \AppBundle\Entity\Transfer $agenceTransfers
     * @return AgencePartner
     */
    public function addAgenceTransfer(\AppBundle\Entity\Transfer $agenceTransfers)
    {
        $this->agenceTransfers[] = $agenceTransfers;

        return $this;
    }

    /**
     * Remove agenceTransfers
     *
     * @param \AppBundle\Entity\Transfer $agenceTransfers
     */
    public function removeAgenceTransfer(\AppBundle\Entity\Transfer $agenceTransfers)
    {
        $this->agenceTransfers->removeElement($agenceTransfers);
    }

    /**
     * Get agenceTransfers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgenceTransfers()
    {
        return $this->agenceTransfers;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return AgencePartner
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

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     * @return AgencePartner
     */
    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set posteRefernet
     *
     * @param string $posteRefernet
     *
     * @return AgencePartner
     */
    public function setPosteRefernet($posteRefernet)
    {
        $this->posteRefernet = $posteRefernet;

        return $this;
    }

    /**
     * Get posteRefernet
     *
     * @return string
     */
    public function getPosteRefernet()
    {
        return $this->posteRefernet;
    }

    /**
     * Set rc
     *
     * @param string $rc
     *
     * @return AgencePartner
     */
    public function setRc($rc)
    {
        $this->rc = $rc;

        return $this;
    }

    /**
     * Get rc
     *
     * @return string
     */
    public function getRc()
    {
        return $this->rc;
    }


    /**
     * Add b2bEnvoice
     *
     * @param \AppBundle\Entity\B2BInvoice $b2bEnvoice
     *
     * @return AgencePartner
     */
    public function addB2bEnvoice(\AppBundle\Entity\B2BInvoice $b2bEnvoice)
    {
        $this->b2bEnvoices[] = $b2bEnvoice;

        return $this;
    }

    /**
     * Remove b2bEnvoice
     *
     * @param \AppBundle\Entity\B2BInvoice $b2bEnvoice
     */
    public function removeB2bEnvoice(\AppBundle\Entity\B2BInvoice $b2bEnvoice)
    {
        $this->b2bEnvoices->removeElement($b2bEnvoice);
    }

    /**
     * Get b2bEnvoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getB2bEnvoices()
    {
        return $this->b2bEnvoices;
    }

    /**
     * Add monthlyB2bEnvoice
     *
     * @param \AppBundle\Entity\MonthlyInvoice $monthlyB2bEnvoice
     *
     * @return AgencePartner
     */
    public function addMonthlyB2bEnvoice(\AppBundle\Entity\MonthlyInvoice $monthlyB2bEnvoice)
    {
        $this->monthlyB2bEnvoices[] = $monthlyB2bEnvoice;

        return $this;
    }

    /**
     * Remove monthlyB2bEnvoice
     *
     * @param \AppBundle\Entity\MonthlyInvoice $monthlyB2bEnvoice
     */
    public function removeMonthlyB2bEnvoice(\AppBundle\Entity\MonthlyInvoice $monthlyB2bEnvoice)
    {
        $this->monthlyB2bEnvoices->removeElement($monthlyB2bEnvoice);
    }

    /**
     * Get monthlyB2bEnvoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMonthlyB2bEnvoices()
    {
        return $this->monthlyB2bEnvoices;
    }



    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Agence
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


    /**
     * Set isPrePayment
     *
     * @param boolean $isPrePayment
     *
     * @return AgencePartner
     */
    public function setIsPrePayment($isPrePayment)
    {
        $this->isPrePayment = $isPrePayment;

        return $this;
    }

    /**
     * Get isPrePayment
     *
     * @return boolean
     */
    public function getIsPrePayment()
    {
        return $this->isPrePayment;
    }
}
