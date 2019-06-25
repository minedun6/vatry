<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 18:14
 */

namespace AppBundle\DataFixtures;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractFixtureContainerAware implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $logFile;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function setLogFile($filename){
        $this->logFile = $filename;
        file_put_contents($this->logFile," ===>  ".date('d/m/Y H:i:s')."  <=====\n",FILE_APPEND);
    }

    public function log($msg){
        $msg = $msg."\n";
        echo $msg;
        file_put_contents($this->logFile,$msg,FILE_APPEND);
    }

    public function logLine(){
        file_put_contents($this->logFile,"===========================================\n",FILE_APPEND);
    }

}