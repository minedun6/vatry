<?php

namespace AppBundle\Entity;

use AppBundle\Utilities\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * BalanceHistory
 *
 * @ORM\Table(name="balance_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BalanceHistoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BalanceHistory
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
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;


    /**
     * @var Balance
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Balance",inversedBy="history")
     */
    private $balance;

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
     * Set amount
     *
     * @param float $amount
     *
     * @return BalanceHistory
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return number_format($this->amount, 2, '.', '');
    }

    /**
     * Set balance
     *
     * @param \AppBundle\Entity\Balance $balance
     *
     * @return BalanceHistory
     */
    public function setBalance(\AppBundle\Entity\Balance $balance = null)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return \AppBundle\Entity\Balance
     */
    public function getBalance()
    {
        return $this->balance;
    }
}
