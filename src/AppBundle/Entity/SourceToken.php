<?php

namespace AppBundle\Entity;

use AppBundle\Utilities\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * SourceToken
 *
 * @ORM\Table(name="source_token")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SourceTokenRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SourceToken
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
     * @ORM\Column(name="token", type="string", length=255,unique=true)
     */
    private $token;

    /**
     * @var bool
     *
     * @ORM\Column(name="expired", type="boolean", nullable=true)
     */
    private $expired;

    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Partner",inversedBy="tokens")
     */
    private $partner;

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
     * Set token
     *
     * @param string $token
     * @return SourceToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return SourceToken
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set partner
     *
     * @param \AppBundle\Entity\Partner $partner
     * @return SourceToken
     */
    public function setPartner(\AppBundle\Entity\Partner $partner = null)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Get partner
     *
     * @return \AppBundle\Entity\Partner 
     */
    public function getPartner()
    {
        return $this->partner;
    }
}
