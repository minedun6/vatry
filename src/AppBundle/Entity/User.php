<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
//class User implements AdvancedUserInterface, \Serializable
{

    const TYPE_PARTNER = 'partner';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_AGENT = 'agent';
    const TYPE_ADMIN = 'ADMIN';
    const TYPE_AGENT_ADMIN = 'agentAdmin';
    const TYPE_COMMERCIAL = 'commercial';
    const TYPE_PARTNERAGENCY = 'partneragency';
    const TYPE_SECRETARY= 'secretary';
    const TYPE_RELAY_CUSTOMER= 'relayCustomer';

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
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15)
     */
    private $type;

//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="deleted_at", type="datetime",nullable=true)
//     */
//    private $deletedAT;


    /**
     * @var Person
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Person", inversedBy="user")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transfer",mappedBy="createdBy")
     */
    private $transfers;


    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Partner",inversedBy="users")
     */
    private $partner;

    /**
     * @var agencePartner
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\AgencePartner",inversedBy="user")
     */
    private $agencePartner;

    /**
     * @var string
     * @ORM\Column(name="salt",type="string",length=255)
     */
    private $salt;

    /**
     * @var Transfer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AgencePartner",mappedBy="createdBy")
     */
    private $agences;

    /**
     * @var RelayCustomerDetail
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\RelayCustomerDetail", mappedBy="user")
     */
    private $relayCustomerDetail;

    /**
     * @var RelayCustomerDetail
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RelayCustomerDetail", mappedBy="createdBy")
     */
    private $relayCustomers;

    /**
     * @var Balance
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Balance",mappedBy="user")
     */
    private $balances;

//    /**
//     * @ORM\Column(name="is_active", type="boolean")
//     */
//    private $isActive;

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
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return User
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

//    /**
//     * Set deletedAT
//     *
//     * @param \DateTime $deletedAT
//     * @return User
//     */
//    public function setDeletedAT($deletedAT)
//    {
//        $this->deletedAT = $deletedAT;
//
//        return $this;
//    }
//
//    /**
//     * Get deletedAT
//     *
//     * @return \DateTime
//     */
//    public function getDeletedAT()
//    {
//        return $this->deletedAT;
//    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return User
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
     * Constructor
     */
    public function __construct()
    {
        $this->transfers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
        $this->deletedAT = null;
    }

    /**
     * Add transfers
     *
     * @param \AppBundle\Entity\Transfer $transfers
     * @return User
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
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set partner
     *
     * @param \AppBundle\Entity\Partner $partner
     * @return User
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

    /**
     * Set agencePartner
     *
     * @param \AppBundle\Entity\AgencePartner $agencePartner
     * @return User
     */
    public function setAgencePartner(\AppBundle\Entity\AgencePartner $agencePartner = null)
    {
        $this->agencePartner = $agencePartner;

        return $this;
    }

    /**
     * Get agencePartner
     *
     * @return \AppBundle\Entity\AgencePartner
     */
    public function getAgencePartner()
    {
        return $this->agencePartner;
    }

    /**
     * Add agences
     *
     * @param \AppBundle\Entity\AgencePartner $agences
     * @return User
     */
    public function addAgence(\AppBundle\Entity\AgencePartner $agences)
    {
        $this->agences[] = $agences;

        return $this;
    }

    /**
     * Remove agences
     *
     * @param \AppBundle\Entity\AgencePartner $agences
     */
    public function removeAgence(\AppBundle\Entity\AgencePartner $agences)
    {
        $this->agences->removeElement($agences);
    }

    /**
     * Get agences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgences()
    {
        return $this->agences;
    }


    public static function getUserSource($type)
    {
        switch (strtolower($type)) {
            case 'agent' :
                return 'Accueil';
            case 'secretary' :
                return 'Secrétaire';
            case 'agentadmin' :
                return 'Commercial';
            case 'customer' :
                return 'Web';
            case 'relaycustomer' :
                return 'Web';
            case 'admin' :
                return 'admin';
            default:
                return '';
        }
    }

    /**
     * Add balance
     *
     * @param \AppBundle\Entity\Balance $balance
     *
     * @return User
     */
    public function addBalance(\AppBundle\Entity\Balance $balance)
    {
        $this->balances[] = $balance;

        return $this;
    }

    /**
     * Remove balance
     *
     * @param \AppBundle\Entity\Balance $balance
     */
    public function removeBalance(\AppBundle\Entity\Balance $balance)
    {
        $this->balances->removeElement($balance);
    }

    /**
     * Get balances
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBalances()
    {
        return $this->balances;
    }

    /**
     * Set relayCustomerDetail
     *
     * @param \AppBundle\Entity\RelayCustomerDetail $relayCustomerDetail
     *
     * @return User
     */
    public function setRelayCustomerDetail(\AppBundle\Entity\RelayCustomerDetail $relayCustomerDetail = null)
    {
        $this->relayCustomerDetail = $relayCustomerDetail;

        return $this;
    }

    /**
     * Get relayCustomerDetail
     *
     * @return \AppBundle\Entity\RelayCustomerDetail
     */
    public function getRelayCustomerDetail()
    {
        return $this->relayCustomerDetail;
    }

    /**
     * Add relayCustomer
     *
     * @param \AppBundle\Entity\RelayCustomerDetail $relayCustomer
     *
     * @return User
     */
    public function addRelayCustomer(\AppBundle\Entity\RelayCustomerDetail $relayCustomer)
    {
        $this->relayCustomers[] = $relayCustomer;

        return $this;
    }

    /**
     * Remove relayCustomer
     *
     * @param \AppBundle\Entity\RelayCustomerDetail $relayCustomer
     */
    public function removeRelayCustomer(\AppBundle\Entity\RelayCustomerDetail $relayCustomer)
    {
        $this->relayCustomers->removeElement($relayCustomer);
    }

    /**
     * Get relayCustomers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelayCustomers()
    {
        return $this->relayCustomers;
    }

//    public function isAccountNonExpired()
//    {
//        return true;
//    }
//
//    public function isAccountNonLocked()
//    {
//        return true;
//    }
//
//    public function isCredentialsNonExpired()
//    {
//        return true;
//    }
//
//    public function isEnabled()
//    {
//        return $this->isActive;
//    }
//
//    /**
//     * @see \Serializable::serialize()
//     */
//    public function serialize()
//    {
//        return serialize(array(
//            $this->id,
//            $this->username,
//            $this->password,
//            // $this->salt,
//            $this->isActive
//        ));
//    }
//
//    /**
//     * @see \Serializable::unserialize()
//     */
//    public function unserialize($serialized)
//    {
//        list (
//            $this->id,
//            $this->username,
//            $this->password,
//            // $this->salt,
//            $this->isActive
//            ) = unserialize($serialized);
//    }
}
