<?php

namespace AppBundle\Entity;

use AppBundle\Utilities\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
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
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     */
    private $person;

    /**
     * @var string
     * @ORM\Column(name="credit_carte_reference",type="string",length=50,nullable=true)
     */
    private $creditCardReference;


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
     * Set type
     *
     * @param string $type
     * @return Payment
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
     * Set amount
     *
     * @param float $amount
     * @return Payment
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
        return $this->amount;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return Payment
     */
    public function setPerson(\AppBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \AppBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set creditCardReference
     *
     * @param string $creditCardReference
     *
     * @return Transfer
     */
    public function setCreditCardReference($creditCardReference)
    {
        $this->creditCardReference = $creditCardReference;

        return $this;
    }

    /**
     * Get creditCardReference
     *
     * @return string
     */
    public function getCreditCardReference()
    {
        return $this->creditCardReference;
    }
}
