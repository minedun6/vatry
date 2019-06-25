<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utilities\TimestampableTrait;

/**
 * MonthlyInvoice
 *
 * @ORM\Table(name="monthly_invoice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MonthlyInvoiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MonthlyInvoice
{
    use TimestampableTrait;

    const STATUS_PAID_B2B = 'paid_b2b'; // paiement paiyé en cas de b2b
    const STATUS_WAIT_B2B = 'wait_b2b'; // paiement en attente en cas de b2b

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
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="netPrice", type="float")
     */
    private $netPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50)
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalPerson", type="integer")
     */
    private $totalPerson;

    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AgencePartner",inversedBy="monthlyB2bEnvoices")
     */
    private $partnerAgency;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\B2BInvoice",mappedBy="monthlyB2bEnvoice")
     */
    private $b2bEnvoices;

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
     * Set price
     *
     * @param float $price
     *
     * @return MonthlyInvoice
     */
    public function setPrice($price)
    {
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
     * Set netPrice
     *
     * @param float $netPrice
     *
     * @return MonthlyInvoice
     */
    public function setNetPrice($netPrice)
    {
        $this->netPrice = $netPrice;

        return $this;
    }

    /**
     * Get netPrice
     *
     * @return float
     */
    public function getNetPrice()
    {
        return $this->netPrice;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return MonthlyInvoice
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->b2bEnvoices = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set partnerAgency
     *
     * @param \AppBundle\Entity\AgencePartner $partnerAgency
     *
     * @return MonthlyInvoice
     */
    public function setPartnerAgency(\AppBundle\Entity\AgencePartner $partnerAgency = null)
    {
        $this->partnerAgency = $partnerAgency;

        return $this;
    }

    /**
     * Get partnerAgency
     *
     * @return \AppBundle\Entity\AgencePartner
     */
    public function getPartnerAgency()
    {
        return $this->partnerAgency;
    }

    /**
     * Add b2bEnvoice
     *
     * @param \AppBundle\Entity\B2BInvoice $b2bEnvoice
     *
     * @return MonthlyInvoice
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
     * Set year
     *
     * @param integer $year
     *
     * @return MonthlyInvoice
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param integer $month
     *
     * @return MonthlyInvoice
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set totalPerson
     *
     * @param integer $totalPerson
     *
     * @return MonthlyInvoice
     */
    public function setTotalPerson($totalPerson)
    {
        $this->totalPerson = $totalPerson;

        return $this;
    }

    /**
     * Get totalPerson
     *
     * @return integer
     */
    public function getTotalPerson()
    {
        return $this->totalPerson;
    }

    public function getPaymentType() {
        return $this->b2bEnvoices[0]->getTransfer()->getPaymentType();
    }

    public function getPaymentTypeFacture() {
        return $this->b2bEnvoices[0]->getTransfer()->getPaymentTypeFacture();
    }

    public function getInvoiceNumber() {
        return 'FB-'. $this->getId();
    }

    public function getReference() {
        $ref = $this->getInvoiceNumber() . '-';

        if($this->getMonth() < 10) $ref = $ref . '0';

        $ref = $ref . $this->getMonth() . $this->getYear();

        return $ref;
    }
}
