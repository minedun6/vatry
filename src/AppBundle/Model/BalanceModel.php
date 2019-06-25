<?php
/**
 * Created by PhpStorm.
 * User: khoubeib
 * Date: 7/15/2016
 * Time: 12:11 PM
 */

namespace AppBundle\Model;


use AppBundle\Entity\User;

class BalanceModel
{
    private $id;
    private $cb;
    private $cb_vad;
    private $cash;
    private $received;
    private $balance;
    private $user;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return float
     */
    public function getReceived()
    {
        return number_format($this->received, 2, '.', '');
    }

    /**
     * @param float $received
     */
    public function setReceived($received)
    {
        $this->received = $received;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return number_format($this->balance, 2, '.', '');
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return float
     */
    public function getCash()
    {
        return number_format($this->cash, 2, '.', '');
    }

    /**
     * @param float $cash
     */
    public function setCash($cash)
    {
        $this->cash = $cash;
    }

    /**
     * @return float
     */
    public function getCbVad()
    {
        return number_format($this->cb_vad, 2, '.', '');
    }

    /**
     * @param float $cb_vad
     */
    public function setCbVad($cb_vad)
    {
        $this->cb_vad = $cb_vad;
    }

    /**
     * @return float
     */
    public function getCb()
    {
        return number_format($this->cb, 2, '.', '');
    }

    /**
     * @param float $cb
     */
    public function setCb($cb)
    {
        $this->cb = $cb;
    }

    /**
     * @return \DateTime
     */
    public function getBalanceDate()
    {
        return $this->balanceDate;
    }

    /**
     * @param \DateTime $balanceDate
     */
    public function setBalanceDate($balanceDate)
    {
        $this->balanceDate = $balanceDate;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }


}