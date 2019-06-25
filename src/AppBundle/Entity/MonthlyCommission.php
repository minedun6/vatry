<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utilities\TimestampableTrait;

/**
 * MonthlyCommission
 *
 * @ORM\Table(name="monthly_commission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MonthlyCommissionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MonthlyCommission
{
    use TimestampableTrait;

    const STATUS_PAID_COM = 'paid_commission'; // paiement paiyé en cas de b2b
    const STATUS_WAIT_COM = 'wait_commission'; // paiement en attente en cas de b2b

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
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RelayCustomerDetail")
     */
    private $clientRelay;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param float $commission
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return Partner
     */
    public function getClientRelay()
    {
        return $this->clientRelay;
    }

    /**
     * @param Partner $clientRelay
     */
    public function setClientRelay($clientRelay)
    {
        $this->clientRelay = $clientRelay;
    }

    /**
     * @return string
     */
    public function getReference() {
        return $this->getId() . '-' . $this->getMonth() . '-' . $this->getYear();
    }
}
