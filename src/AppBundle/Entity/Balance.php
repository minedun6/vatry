<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utilities\TimestampableTrait;

/**
 * Balance
 *
 * @ORM\Table(name="balance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BalanceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Balance
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
     * @ORM\Column(name="cash", type="float", nullable=true)
     */
    private $cash;

    /**
     * @var float
     *
     * @ORM\Column(name="cb", type="float", nullable=true)
     */
    private $cb;

    /**
     * @var float
     *
     * @ORM\Column(name="cb_vad", type="float", nullable=true)
     */
    private $cbVad;

    /**
     * @var float
     *
     * @ORM\Column(name="received", type="float", nullable=true)
     */
    private $received;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float", nullable=true)
     */
    private $balance;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="balances")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="balance_date", type="datetime", nullable=true)
     */
    private $balanceDate;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BalanceHistory",mappedBy="balance")
     *
     */
    private $history;

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
     * Set cash
     *
     * @param float $cash
     *
     * @return Balance
     */
    public function setCash($cash)
    {
        $this->cash = $cash;

        return $this;
    }

    /**
     * Get cash
     *
     * @return float
     */
    public function getCash()
    {
        return number_format($this->cash, 2, '.', '');
    }

    /**
     * Set cb
     *
     * @param float $cb
     *
     * @return Balance
     */
    public function setCb($cb)
    {
        $this->cb = $cb;

        return $this;
    }

    /**
     * Get cb
     *
     * @return float
     */
    public function getCb()
    {
        return number_format($this->cb, 2, '.', '');
    }

    /**
     * Set cbVad
     *
     * @param float $cbVad
     *
     * @return Balance
     */
    public function setCbVad($cbVad)
    {
        $this->cbVad = $cbVad;

        return $this;
    }

    /**
     * Get cbVad
     *
     * @return float
     */
    public function getCbVad()
    {
        return number_format($this->cbVad, 2, '.', '');
    }

    /**
     * Set received
     *
     * @param float $received
     *
     * @return Balance
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get received
     *
     * @return float
     */
    public function getReceived()
    {
        return number_format($this->received, 2, '.', '');;
    }


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Balance
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
     * Set balance
     *
     * @param float $balance
     *
     * @return Balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set balanceDate
     *
     * @param \DateTime $balanceDate
     *
     * @return Balance
     */
    public function setBalanceDate(\DateTime $balanceDate)
    {
        $this->balanceDate = $balanceDate;

        return $this;
    }

    /**
     * Get balanceDate
     *
     * @return \DateTime
     */
    public function getBalanceDate()
    {
        return $this->balanceDate;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->history = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add history
     *
     * @param \AppBundle\Entity\BalanceHistory $history
     *
     * @return Balance
     */
    public function addHistory(\AppBundle\Entity\BalanceHistory $history)
    {
        $this->history[] = $history;

        return $this;
    }

    /**
     * Remove history
     *
     * @param \AppBundle\Entity\BalanceHistory $history
     */
    public function removeHistory(\AppBundle\Entity\BalanceHistory $history)
    {
        $this->history->removeElement($history);
    }

    /**
     * Get history
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }
}
