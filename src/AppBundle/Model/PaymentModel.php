<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 05/06/2016
 * Time: 20:46
 */

namespace AppBundle\Model;


use AppBundle\Entity\Transfer;

class PaymentModel {

    /**
     * @var Transfer
     */
    private $transfer;

    private $tpe;

    private $society;

    private $urlReturn;

    private $urlReturnOK;

    private $urlReturnNOK;

    private $version;

    private $currency;

    private $text;

    private $lang;

    private $mac;

    public function __construct(Transfer $transfer,
                                $mac,
                                $tpe,
                                $society,
                                $urlReturn,
                                $urlReturnOk,
                                $urlReturnNOk,
                                $action,
                                $version,
                                $currency = 'EUR',
                                $text = '',
                                $lang = 'FR',
                                $options = []){

        $this->transfer     = $transfer;
        $this->tpe          = $tpe;
        $this->society      = $society;
        $this->urlReturn    = $urlReturn;
        $this->urlReturnOK  = $urlReturnOk;
        $this->urlReturnNOK = $urlReturnNOk;
        $this->version      = $version;
        $this->currency     = $currency;
        $this->text         = $text;
        $this->lang         = $lang;
        $this->mac          = $mac;
        $this->options      = $options;
        $this->action       = $action;
    }

    public function getVersion(){
        return strval($this->version);
    }

    public function getTpe(){
        return $this->tpe;
    }

    public function getDate(){
        return $this->transfer->getCreatedAt()->format('d/m/Y:H:i:s');
    }

    public function getAmount(){
        return strval(number_format($this->transfer->getPrice(),2,'.','')).$this->currency;
    }

    public function getReference(){
        return $this->transfer->getReferenceWithBank();
    }

    public function getText(){
        return $this->text;
    }

    public function getMail(){
        return $this->transfer->getCreatedBy()->getEmail();
    }

    public function getLang(){
        return $this->lang;
    }

    public function getSociety(){
        return $this->society;
    }

    public function getReturnUrl(){
        return $this->urlReturn;
    }

    public function getReturnUrlOk(){
        return $this->urlReturnOK;
    }

    public function getReturnUrlNOk(){
        return $this->urlReturnNOK;
    }

    public function getMac(){
        return $this->mac;
    }

    public function getOptions(){
        $data = '';
        if (count($this->options)>0){
            foreach($this->options as $key => $value){
                $data .= $key.'='.$value.'&';
            }
            $data[strlen($data)-1] = '';
        }
        return $data;
    }

    public function getAction(){
        return $this->action;
    }

}