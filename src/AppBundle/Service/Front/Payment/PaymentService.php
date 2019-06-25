<?php

/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 05/06/2016
 * Time: 20:37
 */

namespace AppBundle\Service\Front\Payment;


use AppBundle\Entity\Transfer;
use AppBundle\Entity\PorteAPortePrice;
use AppBundle\Model\PaymentModel;
use AppBundle\Service\Front\Transfer\CommonTransferService;
use AppBundle\Utilities\Helpers;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class PaymentService
{
    private $em;

    private $tpe;

    private $secretKey;

    private $society;

    private $router;

    private $action;

    private $version;

    private $commonTransferService;

    private $logger;

    private $logDir;

    public function __construct(EntityManager $em,
                                Router $router,
                                Logger $logger,
                                $tpe,
                                $secretKey,
                                $society,
                                $action,
                                $version,
                                $logDir)
    {
        $this->em = $em;
        $this->tpe = $tpe;
        $this->secretKey = $secretKey;
        $this->router = $router;
        $this->society = $society;
        $this->action = $action;
        $this->version = $version;
        $this->logger = $logger;
        $this->logDir = $logDir;
    }

    public function createPaymentModel(Transfer $transfer)
    {

        //Todo To Complete by urls
        $returnUrl = $this->router->generate('homepage', [], true);
        $returnUrlOK = $this->router->generate('payment_result', ['yesNo' => '1'], true);
        $returnUrlNOK = $this->router->generate('payment_result', ['yesNo' => '0'], true);


        $model = new PaymentModel(
            $transfer,
            $this->generateMAC($transfer),
            $this->getTpeForTransfer($transfer),
            $this->society,
            $returnUrl,
            $returnUrlOK,
            $returnUrlNOK,
            $this->action,
            $this->version);

        return $model;
    }

    public function getTpeForTransfer(Transfer $transfer)
    {
        return $this->tpe[$this->paymentType($transfer)];
    }

    /**
     * @param Transfer $transfer
     * @return string immediat|later
     */
    public function paymentType(Transfer $transfer)
    {
        if( $transfer->getType() == Transfer::PORTEAPORTE_TRANSFERT_To_TOWN) {


            $porteAport = $this->em->getRepository("AppBundle:PorteAPortePrice")->findOneBy(array(
                    'zipCode' => $transfer->getLocation()->getZipCode())
            );


            //var_dump('agglo : '.$porteAport->getAgglomeration()->getId());
            if ($porteAport && $porteAport->getAgglomeration()->getId() == 2 || $porteAport && $porteAport->getAgglomeration()->getId() == 3) {
                //Navette A destination de l'aeroport Paris vatry et Vol From Vatry
                if (
                    ($transfer->getDirection() == Transfer::TO_VATRY) &&
                    (strtotime($transfer->getFlight()->getTime()->format('H:i')) >= strtotime('10:30')) &&
                    (strtotime($transfer->getFlight()->getTime()->format('H:i')) <= strtotime('22:30'))
                ) {
                    return 'immediat';
                } else {
                    return 'later';
                }
                //A partir de l'aeropport Paris Vatry et vol To Paris Vatry
                if (
                    ($transfer->getDirection() == Transfer::FROM_VATRY) &&
                    (strtotime($transfer->getFlight()->getTime()->format('H:i')) >= strtotime('08:00')) &&
                    (strtotime($transfer->getFlight()->getTime()->format('H:i')) <= strtotime('20:00'))
                ) {
                    return 'immediat';
                } else {
                    return 'later';
                }
            }
        }
        if (
            $transfer->getType() == Transfer::PRIVATE_TRANSFER_TO_TOWN ||
            $transfer->getType() == Transfer::PRIVATE_TRANSFER_TO_AIRPORT ||
            $transfer->getType() == Transfer::PARIS_TRANSFER ||
            $transfer->getType() == Transfer::GARE_TRANSFER ||
            $transfer->getType() == Transfer::PARTICULAR_COMMAND ||
            $transfer->getType() == Transfer::INTERVILLE_TRANSFERT_To_TOWN ||
            $transfer->getType() == Transfer::PORTEAPORTE_TRANSFERT_To_TOWN ||
            $transfer->getType() == Transfer::PARIS_AIRPORT )
        {
            return 'immediat';
        } else {
            return 'later';
        }
    }

    public function getSecretKeyForTransfer(Transfer $transfer)
    {
        if (
            $this->paymentType($transfer) == 'immediat'
        ) {
            $secretKey = $this->secretKey['immediat'];
        } else {
            $secretKey = $this->secretKey['later'];
        }
        return $secretKey;
    }

    /**
     * @param Transfer $transfer
     * @return string
     */
    public function generateReturnMAC($dataReturn, $transfer)
    {

        $data = [];
        $data[] = $dataReturn['TPE']; //tpe
        $data[] = str_replace("\\", "", $dataReturn['date']); //date
        $data[] = $dataReturn['montant']; //montant
        $data[] = $dataReturn['reference']; // reference
        $data[] = $dataReturn['texte-libre'];  //text libre
        $data[] = '3.0'; //version
        $data[] = $dataReturn['code-retour']; // code retour
        $data[] = $dataReturn['cvx']; //cvx
        $data[] = $dataReturn['vld']; //vld
        $data[] = $dataReturn['brand']; //brand
        $data[] = $dataReturn['status3ds']; //status3ds
        $data[] = $dataReturn['numauto']; //numauto
        $data[] = $dataReturn['motifrefus'];
        $data[] = $dataReturn['originecb'];
        $data[] = $dataReturn['bincb'];
        $data[] = $dataReturn['hpancb'];
        $data[] = $dataReturn['ipclient'];
        $data[] = $dataReturn['originetr'];
        $data[] = $dataReturn['veres'];
        $data[] = $dataReturn['pares'];
        $data[] = ''; // pour avoir une *  la fin
        $macPlain = implode('*', $data);

        $this->log("Mac retour que nous avons calculé" . json_encode($macPlain));
        $mac = hash_hmac('sha1', $macPlain, $this->_getUsableKey($this->getSecretKeyForTransfer($transfer)));
        return $mac;
    }


    /**
     * @param Transfer $transfer
     * @return string
     */
    public function generateMAC(Transfer $transfer)
    {

        $data = [];
        $data[] = $this->getTpeForTransfer($transfer); //tpe
        $data[] = $transfer->getCreatedAt()->format('d/m/Y:H:i:s'); //date
        $data[] = strval(number_format($transfer->getPrice(), 2, '.', '')) . 'EUR'; //montant
        $data[] = $transfer->getReferenceWithBank(); // reference
        $data[] = '';  //text libre
        $data[] = $this->version; //version
        $data[] = 'FR'; // langue
        $data[] = $this->society; //societe
        $data[] = $transfer->getCreatedBy()->getEmail(); //mail
        $data[] = ''; //nbrech
        $data[] = ''; //datech1
        $data[] = ''; //montantech1
        $data[] = ''; //dateech2
        $data[] = ''; //montantech2
        $data[] = ''; //dateech3
        $data[] = ''; //montantech3
        $data[] = ''; //dateech4
        $data[] = ''; //montantech4
        $data[] = ''; //options

        $macPlain = implode('*', $data);


        $mac = hash_hmac('sha1', $macPlain, $this->_getUsableKey($this->getSecretKeyForTransfer($transfer)));
        return $mac;
    }

    /**
     * @param $data
     * @return bool
     */
    public function verifyDataFomBank($data)
    {
        $refTransfer = Helpers::issetOrNull($data, 'reference');
        if ($refTransfer == null) {
            return false;
        }
        $transfer = $this->em->getRepository("AppBundle:Transfer")->findOneBy(array(
            'referenceWithBank' => $refTransfer
        ));
        if ($transfer == null) {

            return false;
        }
        // le mac généré de l'interface retour
        $mac = $this->generateReturnMAC($data, $transfer);
        $this->log("Mac retour que nous avons calculé" . json_encode($mac));
        $this->log("mac envoyée par la plateforme " . json_encode(Helpers::issetOrNull($data, 'MAC')));

        if (strtoupper($mac) != strtoupper(Helpers::issetOrNull($data, 'MAC'))) {
            return false;
        }
        return true;
    }

    private function _getUsableKey($oEpt)
    {

        $hexStrKey = substr($oEpt, 0, 38);
        $hexFinal = "" . substr($oEpt, 38, 2) . "00";
        $cca0 = ord($hexFinal);
        if ($cca0 > 70 && $cca0 < 97)
            $hexStrKey .= chr($cca0 - 23) . substr($hexFinal, 1, 1);
        else {
            if (substr($hexFinal, 1, 1) == "M")
                $hexStrKey .= substr($hexFinal, 0, 1) . "0";
            else
                $hexStrKey .= substr($hexFinal, 0, 2);
        }

        return pack("H*", $hexStrKey);
    }

    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->logDir . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

}
