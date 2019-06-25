<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 30/04/2016
 * Time: 08:55
 */

namespace AppBundle\Service\General;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class SourceService {

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $em;

    private $sourceLifeTime;

    public function __construct(Session $session,
                                EntityManager $entityManager,
                                $sourceLifeTime
){
        $this->session = $session;
        $this->em = $entityManager;
        $this->sourceLifeTime = $sourceLifeTime;
    }

    public function getSource(){

        if ($this->session->has('source_token')){
            return $this->session->get('source_token');
        }
        return null;
    }

    public function getSourcePartner(){
        $sourceId = intval($this->getSource());
        if ($sourceId){
            $partner = $this->em
                ->getRepository("AppBundle:Partner")
                ->find($sourceId);
        }else{
            $partner = null;
        }
        return $partner;
    }

    public function setSource($token){

        $token = $this->em->getRepository("AppBundle:SourceToken")->findOneBy(array(
                'token' => $token  )
        );

        if (!$token){
            $this->clearSource();
            return ;
        }

        if (!$token->getExpired()){
            $this->session->set('source_token',$token->getPartner()->getId());
            $this->session->set('source_token_time',time());
            return true;
        }
        return false;
    }

    public function clearSource(){

        if ($this->session->has('source_token')){
            $this->session->remove('source_token');
        }

        if ($this->session->has('source_token_time')){
            $this->session->remove('source_token_time');
        }


    }

    public function verifySource(){
        if ($this->session->has('source_token_time')){
            $t = intval($this->session->get('source_token_time'));
            $t2 = time();
            $diff = ($t2 - $t)/60;
            if ($diff >= $this->sourceLifeTime){
                $this->clearSource();
            }
        }
    }
}