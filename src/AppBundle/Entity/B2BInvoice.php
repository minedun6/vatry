<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utilities\TimestampableTrait;

/**
 * B2BInvoice
 *
 * @ORM\Table(name="b2_b_invoice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\B2BInvoiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class B2BInvoice
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
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="commission", type="float")
     */
    private $commission;

    /**
     * @var float
     *
     * @ORM\Column(name="netPrice", type="float")
     */
    private $netPrice;

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
     * @var Transfer
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Transfer",inversedBy="b2bEnvoice")
     * @ORM\JoinColumn(name="transfer_id", referencedColumnName="id")
     */
    private $transfer;

    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AgencePartner",inversedBy="b2bEnvoices")
     */
    private $partnerAgency;

    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MonthlyInvoice",inversedBy="b2bEnvoices")
     */
    private $monthlyB2bEnvoice;

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
     * @return B2BInvoice
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
     * Set commission
     *
     * @param float $commission
     *
     * @return B2BInvoice
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set netPrice
     *
     * @param float $netPrice
     *
     * @return B2BInvoice
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
     * Set transfer
     *
     * @param \AppBundle\Entity\Transfer $transfer
     *
     * @return B2BInvoice
     */
    public function setTransfer(\AppBundle\Entity\Transfer $transfer = null)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer
     *
     * @return \AppBundle\Entity\Transfer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }


    /**
     * Set partnerAgency
     *
     * @param \AppBundle\Entity\AgencePartner $partnerAgency
     *
     * @return B2BInvoice
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
     * Set monthlyB2bEnvoice
     *
     * @param \AppBundle\Entity\MonthlyInvoice $monthlyB2bEnvoice
     *
     * @return B2BInvoice
     */
    public function setMonthlyB2bEnvoice(\AppBundle\Entity\MonthlyInvoice $monthlyB2bEnvoice = null)
    {
        $this->monthlyB2bEnvoice = $monthlyB2bEnvoice;

        return $this;
    }

    /**
     * Get monthlyB2bEnvoice
     *
     * @return \AppBundle\Entity\MonthlyInvoice
     */
    public function getMonthlyB2bEnvoice()
    {
        return $this->monthlyB2bEnvoice;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return B2BInvoice
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
     * @return B2BInvoice
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
     * @return B2BInvoice
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
}
