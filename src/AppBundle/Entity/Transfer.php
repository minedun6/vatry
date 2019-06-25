<?php

namespace AppBundle\Entity;

use AppBundle\Service\Validator\Front\DateAllerRetourConstraint\DateAllerConstraint;
use AppBundle\Utilities\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Transfer
 *
 * @ORM\Table(name="transfer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransferRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Transfer
{
    use TimestampableTrait;

    const TO_VATRY = 'to_vatry';
    const FROM_VATRY = 'from_vatry';

    const PRIVATE_TRANSFER_TO_TOWN = 'private_airport_town';
    const PRIVATE_TRANSFER_TO_AIRPORT = 'private_airport_airport';
    const INTERVILLE_TRANSFERT_To_TOWN = 'interville_airporttown';
    const PORTEAPORTE_TRANSFERT_To_TOWN = 'porteaporte_airporttown';
    const GARE_TRANSFER = 'tranfer_gare';
    const PARIS_TRANSFER = 'paris_transfer';
    const PARTICULAR_COMMAND = 'particular_command';
    const PARIS_AIRPORT = 'paris_airport';

    const STATUS_OPEN = 'open'; // transfert ouvert ( il n'est pas passé au paiement)
    const STATUS_PAYMENT = 'payment'; //
    const STATUS_PAID = 'paid'; // payé
    const STATUS_CANCEL = 'cancel';// paiement annulé
    const STATUS_PAID_TEST = 'paid_test'; // paiement en test
    const STATUS_PAID_CANCELED = 'paid_canc'; // paiement annulé
    const STATUS_PAID_PENDING = 'paid_pend'; // paiement en attente en cas de différé
    const STATUS_PAID_RELAY = 'relay_paid'; //paiement par bonus du client relais
    const STATUS_RECOUVE = 'recouve';

    const STATUS_OPEN_B2B = 'open_b2b'; // transfert ouvert en cas de b2b
    const STATUS_PAID_B2B = 'paid_b2b'; // paiement paiyé en cas de b2b
    const STATUS_CANCEL_B2B = 'cancel_b2b'; // paiement annulé en cas de b2b
    const STATUS_VALID_B2B = 'valid_b2b'; // paiement valide en cas de b2b
    const STATUS_WAIT_B2B = 'wait_b2b'; // paiement en attente en cas de b2b

    const STATUS_OPEN_RC = 'open_rc'; // transfert ouvert en cas de rc (client relai)
    const STATUS_PAID_RC = 'paid_rc'; // paiement paiyé en cas de rc
    const STATUS_CANCEL_RC = 'cancel_rc'; // paiement annulé en cas de rc
    const STATUS_VALID_RC = 'valid_rc'; // paiement valide en cas de rc
    const STATUS_WAIT_RC = 'wait_rc'; // paiement en attente en cas de rc

    const TYPE_RELAY_PAY = 'relay_pay';
    const TYPE_CREDIT_CARD = 'credit_card'; // paiement par carte bancaire en cas de immédiat
    const TYPE_CACHE = 'cash'; // paiement par espèce en cas de immédiat
    const TYPE_CHEQUE = 'cheque'; // paiement par chèque
    const TYPE_VIREMENT = 'transfer'; // paiement par virement
    const TYPE_B2B = 'b2b'; // paiement b2b non effectu

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="integer", nullable=true)
     */
    private $qty;

    /**
     * @var int
     *
     * @ORM\Column(name="qtyChild", type="integer", nullable=true)
     */
    private $qtyChild;

    /**
     * @var int
     *
     * @ORM\Column(name="qtyBaby", type="integer", nullable=true)
     */
    private $qtyBaby;

    /**
     * @var bool
     *
     * @ORM\Column(name="roundTrip", type="boolean", nullable=true)
     */
    private $roundTrip;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="tva", type="float", nullable=true)
     */
    private $tva;

    /**
     * @var float
     *
     * @ORM\Column(name="commission", type="float", nullable=true, options={"default":0})
     */
    private $commission;

    /**
     * @var string
     * @ORM\Column(name="address",type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="address_2",type="text", nullable=true)
     */
    private $address2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pickupDate", type="datetime",nullable=true)
     */
    private $pickupDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pickupDate2", type="datetime", nullable=true)
     */
    private $pickupDate2;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(name="type",type="string",length=40,nullable=true)
     *
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="external_flight",type="string",length=40,nullable=true)
     *
     */
    private $externalFlight;

    /**
     * @var string
     * @ORM\Column(name="external_flight_time",type="string",length=8,nullable=true)
     *
     */
    private $externalFlightTime;

    /**
     * @var string
     * @ORM\Column(name="external_flight2",type="string",length=40,nullable=true)
     *
     */
    private $externalFlight2;

    /**
     * @var string
     * @ORM\Column(name="external_flight_time2",type="string",length=40,nullable=true)
     *
     */
    private $externalFlightTime2;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     */
    private $location;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     */
    private $location2;

    /**
     * @var string
     * @ORM\Column(name="direction",type="string",length=50, nullable=true)
     */
    private $direction;

    /**
     * @var Flight
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Flight")
     */
    private $flight;

    /**
     * @var Flight
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Flight")
     */
    private $flight2;

    /**
     * @var Payment
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Payment")
     */
    private $payment;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="transfers")
     */
    private $createdBy;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person",inversedBy="transfers",cascade={"persist"})
     */
    private $passenger;

    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Partner",inversedBy="transfers")
     */
    private $partner;

    /**
     * @var string
     * @ORM\Column(name="reference",type="string",length=100,nullable=true)
     */
    private $reference;

    /**
     * @var string
     * @ORM\Column(name="reference_with_bank",type="string",length=100,nullable=true)
     */
    private $referenceWithBank;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="agenceTransfers")
     */
    private $affectedTo;

    /**
     * @var string
     * @ORM\Column(name="remarques",type="text",nullable=true)
     */
    private $remarques;

    /**
     * @var B2BInvoice
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\B2BInvoice",mappedBy="transfer", cascade={"persist"})
     */
    private $b2bEnvoice;


    /**
     * @ORM\ManyToOne(targetEntity="Driver",inversedBy="transfers",cascade={"persist"})
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    private $driver;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     * @return Transfer
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return integer
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set qtyChild
     *
     * @param integer $qtyChild
     * @return Transfer
     */
    public function setQtyChild($qtyChild)
    {
        $this->qtyChild = $qtyChild;

        return $this;
    }

    /**
     * Get qtyChild
     *
     * @return integer
     */
    public function getQtyChild()
    {
        return $this->qtyChild;
    }

    /**
     * Set qtyBaby
     *
     * @param integer $qtyBaby
     * @return Transfer
     */
    public function setQtyBaby($qtyBaby)
    {
        $this->qtyBaby = $qtyBaby;

        return $this;
    }

    /**
     * Get qtyBaby
     *
     * @return integer
     */
    public function getQtyBaby()
    {
        return $this->qtyBaby;
    }

    /**
     * Set roundTrip
     *
     * @param boolean $roundTrip
     * @return Transfer
     */
    public function setRoundTrip($roundTrip)
    {
        $this->roundTrip = $roundTrip;

        return $this;
    }

    /**
     * Get roundTrip
     *
     * @return boolean
     */
    public function getRoundTrip()
    {
        return $this->roundTrip;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Transfer
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
     * Set tva
     *
     * @param float $tva
     * @return Transfer
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return float
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set pickupDate2
     *
     * @param \DateTime $pickupDate2
     * @return Transfer
     */
    public function setPickupDate2($pickupDate2)
    {
        $this->pickupDate2 = $pickupDate2;

        return $this;
    }

    /**
     * Get pickupDate2
     *
     * @return \DateTime
     */
    public function getPickupDate2()
    {
        return $this->pickupDate2;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Transfer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Transfer
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
     * Set flight
     *
     * @param \AppBundle\Entity\Flight $flight
     * @return Transfer
     */
    public function setFlight(\AppBundle\Entity\Flight $flight = null)
    {
        $this->flight = $flight;

        return $this;
    }

    /**
     * Get flight
     *
     * @return \AppBundle\Entity\Flight
     */
    public function getFlight()
    {
        return $this->flight;
    }

    /**
     * Set payment
     *
     * @param \AppBundle\Entity\Payment $payment
     * @return Transfer
     */
    public function setPayment(\AppBundle\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \AppBundle\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     * @return Transfer
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
     * Set passenger
     *
     * @param \AppBundle\Entity\Person $passenger
     * @return Transfer
     */
    public function setPassenger(\AppBundle\Entity\Person $passenger = null)
    {
        $this->passenger = $passenger;

        return $this;
    }

    /**
     * Get passenger
     *
     * @return \AppBundle\Entity\Person
     */
    public function getPassenger()
    {
        return $this->passenger;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Transfer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return Transfer
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set pickupDate
     *
     * @param \DateTime $pickupDate
     * @return Transfer
     */
    public function setPickupDate($pickupDate)
    {
        $this->pickupDate = $pickupDate;

        return $this;
    }

    /**
     * Get pickupDate
     *
     * @return \DateTime
     */
    public function getPickupDate()
    {
        return $this->pickupDate;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     * @return Transfer
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location2
     *
     * @param \AppBundle\Entity\Location $location2
     * @return Transfer
     */
    public function setLocation2(\AppBundle\Entity\Location $location2 = null)
    {
        $this->location2 = $location2;

        return $this;
    }

    /**
     * Get location2
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation2()
    {
        return $this->location2;
    }

    /**
     * Set flight2
     *
     * @param \AppBundle\Entity\Flight $flight2
     * @return Transfer
     */
    public function setFlight2(\AppBundle\Entity\Flight $flight2 = null)
    {
        $this->flight2 = $flight2;

        return $this;
    }

    /**
     * Get flight2
     *
     * @return \AppBundle\Entity\Flight
     */
    public function getFlight2()
    {
        return $this->flight2;
    }

    /**
     * Set direction
     *
     * @param string $direction
     * @return Transfer
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set externalFlight
     *
     * @param string $externalFlight
     * @return Transfer
     */
    public function setExternalFlight($externalFlight)
    {
        $this->externalFlight = $externalFlight;

        return $this;
    }

    /**
     * Get externalFlight
     *
     * @return string
     */
    public function getExternalFlight()
    {
        return $this->externalFlight;
    }

    /**
     * Set externalFlightTime
     *
     * @param string $externalFlightTime
     * @return Transfer
     */
    public function setExternalFlightTime($externalFlightTime)
    {
        $this->externalFlightTime = $externalFlightTime;

        return $this;
    }

    /**
     * Get externalFlightTime
     *
     * @return string
     */
    public function getExternalFlightTime()
    {
        return $this->externalFlightTime;
    }

    /**
     * Set externalFlight2
     *
     * @param string $externalFlight2
     * @return Transfer
     */
    public function setExternalFlight2($externalFlight2)
    {
        $this->externalFlight2 = $externalFlight2;

        return $this;
    }

    /**
     * Get externalFlight2
     *
     * @return string
     */
    public function getExternalFlight2()
    {
        return $this->externalFlight2;
    }

    /**
     * Set externalFlightTime2
     *
     * @param string $externalFlightTime2
     * @return Transfer
     */
    public function setExternalFlightTime2($externalFlightTime2)
    {
        $this->externalFlightTime2 = $externalFlightTime2;

        return $this;
    }

    /**
     * Get externalFlightTime2
     *
     * @return string
     */
    public function getExternalFlightTime2()
    {
        return $this->externalFlightTime2;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Transfer
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set referenceWithBank
     *
     * @param string $referenceWithBank
     * @return Transfer
     */
    public function setReferenceWithBank($referenceWithBank)
    {
        $this->referenceWithBank = $referenceWithBank;

        return $this;
    }

    /**
     * Get referenceWithBank
     *
     * @return string
     */
    public function getReferenceWithBank()
    {
        return $this->referenceWithBank;
    }

    /**
     * Set partner
     *
     * @param \AppBundle\Entity\Partner $partner
     * @return Transfer
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
     * Set affectedTo
     *
     * @param \AppBundle\Entity\User $affectedTo
     * @return Transfer
     */
    public function setAffectedTo(\AppBundle\Entity\User $affectedTo = null)
    {
        $this->affectedTo = $affectedTo;

        return $this;
    }

    /**
     * Get affectedTo
     *
     * @return \AppBundle\Entity\User
     */
    public function getAffectedTo()
    {
        return $this->affectedTo;
    }

    /**
     * Set remarques
     *
     * @param string $remarques
     * @return Transfer
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;

        return $this;
    }

    /**
     * Get remarques
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
    }


    public function initRef()
    {
        if ($this->reference == null) {
            $ref = "T-";
            $refBank = "T";
            switch ($this->type) {
                case self::PRIVATE_TRANSFER_TO_TOWN :
                    $ref = "TPV-";
                    $refBank = "TPV";
                    break;
                case self::PRIVATE_TRANSFER_TO_AIRPORT :
                    $ref = "TPA-";
                    $refBank = "TPA";
                    break;
                case self::INTERVILLE_TRANSFERT_To_TOWN :
                    $ref = "NPI-";
                    $refBank = "NPI";
                    break;
                case self::PORTEAPORTE_TRANSFERT_To_TOWN :
                    $ref = "NPP-";
                    $refBank = "NPP";
                    break;
                case self::GARE_TRANSFER :
                    if ($this->getLocation()->getZipCode() == Location::GARE_CHALONS) {
                        $ref = "NGC-";
                        $refBank = "NGC";
                    } elseif ($this->getLocation()->getZipCode() == Location::GARE_REIMS) {
                        $ref = "NGR-";
                        $refBank = "NGR";
                    }
                    break;
                case self::PARIS_TRANSFER :
                    $ref = "TP-";
                    $refBank = "TP";
                    break;
                case self::PARTICULAR_COMMAND:
                    $ref = "CP-";
                    $refBank = "CP";
                    break;
                case self::PARIS_AIRPORT:
                    $ref = "PAR-";
                    $refBank = "PAR";
                    break;
            }

            if ($this->direction == self::TO_VATRY) {
                $ref = $ref . "V";
            } else {
                $ref = $ref . "A";
            }

            if ($this->roundTrip) {
                $ref = $ref . "R-";
            } else {
                $ref = $ref . "S-";
            }

            $ref = $ref . date('ymd');

            $ref = $ref . "-" . $this->getId();
            $refBank = $refBank . $this->getId();
            $this->reference = $ref;
            $this->referenceWithBank = $refBank;
        }
    }

    public function dateTransfer($return = false)
    {
        if (!$return) {
            return $this->pickupDate->format('d/m/Y');
        } else {
            return $this->pickupDate2->format('d/m/Y');
        }

    }

    public function hourTransfer($return = false)
    {
        if (!$return) {
            return $this->pickupDate->format('H:i');
        } else {
            return $this->pickupDate2->format('H:i');
        }
    }

    public function rdvLocationTransfer($return = false)
    {
        if (!$return) {
            $location = $this->getLocation();
            $address = $this->address;
        } else {
            $location = $this->getLocation2();
            $address = $this->address2;
        }

        switch ($this->type) {
            case self::PRIVATE_TRANSFER_TO_TOWN :
                return $address . ", " . $location->getZipCode() . ", " . $location->getName();
                break;
            case self::PRIVATE_TRANSFER_TO_AIRPORT :
                return $location->getName();
                break;
            case self::INTERVILLE_TRANSFERT_To_TOWN :
                return $location->getName() . ", " . $location->getZipCode();
                break;
            case self::PORTEAPORTE_TRANSFERT_To_TOWN :
                return $address . ", " . $location->getName() . ", " . $location->getZipCode();
                break;
            case self::PARIS_TRANSFER :
                return $address . ", " . $location->getZipCode() . ", " . $location->getName();
                break;
            case self::GARE_TRANSFER :
                if ($this->getLocation()->getZipCode() == Location::GARE_CHALONS) {
                    return $location->getName();
                } elseif ($this->getLocation()->getZipCode() == Location::GARE_REIMS) {
                    return $location->getName();
                }
                break;
            case self::PARIS_AIRPORT:
                return 'RER / Metro Bibliothèque François <br> Mitterrand (Sortie Avenue de France) 117 Av de France 75013 Paris';
                break;
        }
    }

    public function getPrestationTypeToDisplay()
    {

        switch ($this->type) {
            case self::PRIVATE_TRANSFER_TO_TOWN :
                return "Transfert privé Taxi Aéroport – Domicile";
                break;
            case self::PRIVATE_TRANSFER_TO_AIRPORT :
                return "Transfert privé Taxi Aéroport – Aéroport";
                break;
            case self::INTERVILLE_TRANSFERT_To_TOWN :
                return "Navette Partagée Inter-Villes";
                break;
            case self::PORTEAPORTE_TRANSFERT_To_TOWN :
                return "Navette Partagée Aeroport – Domicile";
                break;
            case self::PARIS_TRANSFER :
                return "Navette partagée Aéroport - Région Parisienne";
                break;
            case self::GARE_TRANSFER :
                if ($this->getLocation()->getZipCode() == Location::GARE_CHALONS) {
                    return "Navette Aéroport - Gare de Châlon-en-Champagne";
                } elseif ($this->getLocation()->getZipCode() == Location::GARE_REIMS) {
                    return "Navette Aéroport – Gare de Reims";
                }
                break;
            case self::PARTICULAR_COMMAND:
                return "Commande Particulière";
                break;
            case self::PARIS_AIRPORT:
                return "Navette Aéroport-Paris";
                break;
        }

    }


    public function getNamePresta()
    {

        switch ($this->type) {
            case self::PRIVATE_TRANSFER_TO_TOWN :
                return "Transfert privé Taxi Aéroport – Domicile";
                break;
            case self::PRIVATE_TRANSFER_TO_AIRPORT :
                return "Transfert privé Taxi Aéroport – Aéroport";
                break;
            case self::INTERVILLE_TRANSFERT_To_TOWN :
                return "Navette Partagée Inter-Villes";
                break;
            case self::PORTEAPORTE_TRANSFERT_To_TOWN :
                return "Navette Partagée Aeroport – Domicile";
                break;
            case self::PARIS_TRANSFER :
                return "Navette partagée Aéroport - Région Parisienne";
                break;
            case self::PARTICULAR_COMMAND :
                return "Commande Particulière";
                break;
            case self::GARE_TRANSFER :
                if ($this->getLocation()->getZipCode() == Location::GARE_CHALONS) {
                    return "Navette Aéroport - Gare de Châlon-en-Champagne";
                } elseif ($this->getLocation()->getZipCode() == Location::GARE_REIMS) {
                    return "Navette Aéroport – Gare de Reims";
                }
                break;
            case self::PARIS_AIRPORT:
                return "Navette Aéroport-Paris";
                break;
        }

    }

    public function getNamePrestaDt()
    {

        switch ($this->type) {
            case self::PRIVATE_TRANSFER_TO_TOWN :
                return "Taxi A – D";
                break;
            case self::PRIVATE_TRANSFER_TO_AIRPORT :
                return "Taxi A – A";
                break;
            case self::INTERVILLE_TRANSFERT_To_TOWN :
                return "Pa Inter-ville";
                break;
            case self::PORTEAPORTE_TRANSFERT_To_TOWN :
                return "Pa A - D";
                break;
            case self::PARIS_TRANSFER :
                return "Pa A – RP";
                break;
            case self::PARTICULAR_COMMAND:
                return "CP";
                break;
            case self::GARE_TRANSFER :
                if ($this->getLocation()->getZipCode() == Location::GARE_CHALONS) {
                    return "Pa A – G 1";
                } elseif ($this->getLocation()->getZipCode() == Location::GARE_REIMS) {
                    return "Pa A – G 2";
                }
                break;
            case self::PARIS_AIRPORT:
                return "PAR";
                break;
        }

    }

    public function getStatutPresta()
    {
        switch ($this->status) {
            case self::STATUS_OPEN :
                return "Ouvert";
                break;
            case self::STATUS_CANCEL :
                return "Annulé";
                break;
            case self::STATUS_PAID_PENDING :
                return "En attente";
                break;
            case self::STATUS_PAID :
                return "Payé";
                break;
            case self::STATUS_PAID_RELAY :
                return "Payé bonus relais";
                break;
            case self::STATUS_RECOUVE :
                return "En attente recouvrement";
                break;
            case self::STATUS_PAID_CANCELED :
                return "Annulé par la banque";
                break;

            /***** B2B ****/
            case self::STATUS_OPEN_B2B :
                return "Ouvert";
                break;
            case self::STATUS_CANCEL_B2B :
                return "Annulé";
                break;
            case self::STATUS_WAIT_B2B :
                return "Attente B2B - " . $this->getReferenceWithBank();;
                break;
            case self::STATUS_PAID_B2B :
                return "Payé";
                break;
            case self::STATUS_VALID_B2B :
                return "Validé";
                break;
        }

    }

    public function getPaymentTypeFacture()
    {
        if ($this->status == $this::STATUS_PAID) {
            if ($this->payment) {
                if ($this->payment->getType() == $this::TYPE_CACHE) {
                    return "Espèce ";
                } elseif ($this->payment->getType() == $this::TYPE_CREDIT_CARD) {
                    return "Carte Bancaire";
                } else {
                    return "VAD";
                }
            } else
                return "VAD";
        } elseif ($this->status == $this::STATUS_PAID_B2B) {
            if ($this->payment) {
                if ($this->payment->getType() == $this::TYPE_CACHE) {
                    return "Espèce ";
                } elseif ($this->payment->getType() == $this::TYPE_CREDIT_CARD) {
                    return "Carte Bancaire";
                } elseif ($this->payment->getType() == $this::TYPE_CHEQUE) {
                    return "Chèque";
                } elseif ($this->payment->getType() == $this::TYPE_VIREMENT) {
                    return "Virement";
                } else {
                    return "VAD";
                }
            } else
                return "VAD";
        } elseif ($this->status == Transfer::STATUS_PAID_RELAY) {
            return "Bonus Relais";
        } else {
            return "";
        }
    }


    public function getPaymentType()
    {
        if ($this->status == $this::STATUS_VALID_B2B) {
            return "Facturé";
        }
        if ($this->status == $this::STATUS_PAID) {
            if ($this->payment) {
                if ($this->payment->getType() == $this::TYPE_CACHE) {
                    return "Espèce ";
                } elseif ($this->payment->getType() == $this::TYPE_CREDIT_CARD) {
                    return "CB - " . $this->getPayment()->getCreditCardReference();
                } else {
                    return "VAD - " . $this->getReferenceWithBank();
                }
            } else
                return "VAD - " . $this->getReferenceWithBank();
        } elseif ($this->status == $this::STATUS_PAID_B2B) {
            if ($this->payment) {
                if ($this->payment->getType() == $this::TYPE_CACHE) {
                    return "Espèce ";
                } elseif ($this->payment->getType() == $this::TYPE_CREDIT_CARD) {
                    return "CB - " . $this->getPayment()->getCreditCardReference();
                } elseif ($this->payment->getType() == $this::TYPE_CHEQUE) {
                    return "CHEQUE - " . $this->getPayment()->getCreditCardReference();
                } elseif ($this->payment->getType() == $this::TYPE_VIREMENT) {
                    return "Virement - " . $this->getPayment()->getCreditCardReference();
                } else {
                    return "VAD - " . $this->getReferenceWithBank();
                }
            } else
                return "VAD - " . $this->getReferenceWithBank();
        } elseif ($this->status == Transfer::STATUS_PAID_RELAY) {
            return "Bonus Relais";
        } else {
            return "";
        }
    }


    public
    function getLieuRDV($return = false)
    {

        if (!$return) {

            switch ($this->type) {
                case self::PRIVATE_TRANSFER_TO_TOWN :
                    return "Transfert Privé Aéroport/Ville";
                    break;
                case self::PRIVATE_TRANSFER_TO_AIRPORT :
                    return "Transfert Privé Aéroport/Aéroport";
                    break;
                case self::INTERVILLE_TRANSFERT_To_TOWN :
                    return "Navette Partagée Inter-ville";
                    break;
                case self::PORTEAPORTE_TRANSFERT_To_TOWN :
                    return "Navette Partagée de Porte à Porte";
                    break;
                case self::PARIS_TRANSFER :
                    return "Navette Partagée de Paris";
                    break;
                case self::GARE_TRANSFER :
                    if ($this->getLocation()->getZipCode() == Location::GARE_CHALONS) {
                        return "Navette Gare de Châlon-en-Champagne";
                    } elseif ($this->getLocation()->getZipCode() == Location::GARE_REIMS) {
                        return "Navette Gare de Reims";
                    }
                    break;
            }
        }
    }

    public function getSourceTransfer()
    {

        // si agence partenaire
        if ($this->getPartner()) {
            return $this->getPartner()->getName();
        } else if (in_array('ROLE_COMMERCIAL', $this->getCreatedBy()->getRoles()) && $this->getAffectedTo() && $this->getCreatedBy() != $this->getAffectedTo() && $this->getAffectedTo()->getAgencePartner()) {
            return $this->getAffectedTo()->getAgencePartner()->getAgence()->getNom();

        } else if (in_array('ROLE_CUSTOMER', $this->getCreatedBy()->getRoles())) {
            return "NDV";
        } else if ($this->getCreatedBy()->getAgencePartner()) {
            return $this->getCreatedBy()->getAgencePartner()->getAgence()->getNom();
        } else if (in_array('ROLE_ADMIN', $this->getCreatedBy()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getCreatedBy()->getRoles()) || in_array('ROLE_SECRETARY', $this->getCreatedBy()->getRoles()) || in_array('ROLE_AGENT', $this->getCreatedBy()->getRoles())) {
            return ($this->getCreatedBy()->getPerson()->getName() . " " . $this->getCreatedBy()->getPerson()->getLastname());
        } else if (in_array('ROLE_RELAY_CUSTOMER', $this->getCreatedBy()->getRoles())) {
            return "Client relais : " . $this->getCreatedBy()->getPerson()->getName() . " " . $this->getCreatedBy()->getPerson()->getLastname();

        }
    }


    /**
     * Set b2bEnvoice
     *
     * @param \AppBundle\Entity\B2BInvoice $b2bEnvoice
     *
     * @return Transfer
     */
    public function setB2bEnvoice(\AppBundle\Entity\B2BInvoice $b2bEnvoice = null)
    {
        $this->b2bEnvoice = $b2bEnvoice;

        return $this;
    }

    /**
     * Get b2bEnvoice
     *
     * @return \AppBundle\Entity\B2BInvoice
     */
    public function getB2bEnvoice()
    {
        return $this->b2bEnvoice;
    }

    public function getFlightNumber($round_trip)
    {
        if ($this->flight) {
            if ($round_trip) {
                if ($this->direction == self::TO_VATRY) {
                    return $this->flight2->getNum() . ' ' . $this->flight2->getFromLocation();
                } else {
                    return $this->flight2->getNum() . ' ' . $this->flight2->getToLocation();
                }
            } else {
                if ($this->direction == self::FROM_VATRY) {
                    return $this->flight->getNum() . ' ' . $this->flight->getFromLocation();
                } else {
                    return $this->flight->getNum() . ' ' . $this->flight->getToLocation();
                }
            }

        } else {
            return '';
        }
    }

    public function getLocations($round_trip)
    {
        if ($this->location) {
            if ($round_trip) {
                if ($this->direction == self::FROM_VATRY) {
                    $result = "<td>" . $this->location2->getName() . "</td>";
                    $result .= "<td> Aéroport Paris-Vatry</td>";
                    return $result;
                } else {
                    $result = "<td> Aéroport Paris-Vatry</td>";
                    $result .= "<td>" . $this->location2->getName() . "</td>";
                    return $result;
                }
            } else {
                if ($this->direction == self::TO_VATRY) {
                    $result = "<td>" . $this->location->getName() . "</td>";
                    $result .= "<td> Aéroport Paris-Vatry</td>";
                    return $result;
                } else {
                    $result = "<td> Aéroport Paris-Vatry</td>";
                    $result .= "<td>" . $this->location->getName() . "</td>";
                    return $result;
                }
            }

        } else {
            if ($this->type == self::PARTICULAR_COMMAND) {
                if ($round_trip) {
                    $result = "<td>" . $this->address2 . "</td>";
                    $result .= "<td>" . $this->address . "</td>";
                    return $result;
                } else {
                    $result = "<td>" . $this->address . "</td>";
                    $result .= "<td>" . $this->address2 . "</td>";
                    return $result;
                }
            }
        }
    }

    public function getNdvPrice()
    {
        if ($this->getType() == self::PRIVATE_TRANSFER_TO_AIRPORT || $this->getType() == self::PRIVATE_TRANSFER_TO_TOWN) {
            if (($this->getAffectedTo() && $this->getAffectedTo()->getAgencePartner())) {
                $prixHT = $this->getPrice() * 100 / 110;
                if ($this->getCommission() && $this->getCommission() != 0)
                    return $this->getPrice();
                else {
                    $commission2 = ($prixHT * $this->getAffectedTo()->getAgencePartner()->getCommission()) / 100;
//                return $this->getPrice() * (1 - ($this->getAffectedTo()->getAgencePartner()->getCommission() / 100));
                    return $this->getPrice() - $commission2;
                }

            } else
                if ($this->getCreatedBy()->getAgencePartner()) {
//                    return $this->getPrice() * (1 - ($this->getCreatedBy()->getAgencePartner()->getCommission() / 100));
                    if ($this->getCommission() && $this->getCommission() != 0)
                        return $this->getPrice();
                    else {
                        $prixHT = $this->getPrice() * 100 / 110;
                        $commission2 = ($prixHT * $this->getCreatedBy()->getAgencePartner()->getCommission()) / 100;
                        return $this->getPrice() - $commission2;
                    }
                } else
                    return $this->getPrice();
        } else {
            return $this->getPrice();
        }
    }


    /**
     * Set driver
     *
     * @param \AppBundle\Entity\Driver $driver
     *
     * @return Transfer
     */
    public function setDriver(\AppBundle\Entity\Driver $driver = null)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get driver
     *
     * @return \AppBundle\Entity\Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }



    /**
     * Set commission
     *
     * @param float $commission
     *
     * @return Transfer
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
}
