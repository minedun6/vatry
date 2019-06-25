<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 18/07/2016
 * Time: 16:28
 */



namespace AppBundle\Service\Back;
use AppBundle\Entity\Flight;
use AppBundle\Entity\PorteAPortePrice;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Date;
use DateTime;
use PHPExcel_IOFactory;

class DataBaseFileImportService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public function flightImportXLSXLSX($file)
    {
        $reader = PHPExcel_IOFactory::createReaderForFile($file);
        $fileObject = $reader->load($file);
        $sheet = $fileObject->getSheet(0);
        $row = 2;
        $log = array();
        $existingRows=array();
        $newRows=array();
        ini_set('max_execution_time', '0');
        while ($sheet->getCellByColumnAndRow(0, $row) != '') {
            $date = $sheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
            $time = $sheet->getCellByColumnAndRow(4, $row)->getFormattedValue();
            $num = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
            $from = $sheet->getCellByColumnAndRow(2, $row)->getFormattedValue();
            $to = $sheet->getCellByColumnAndRow(3, $row)->getFormattedValue();
            $country = $sheet->getCellByColumnAndRow(5, $row)->getFormattedValue();

            if (!DateTime::createFromFormat('m-d-y', $date)) {
                $log[] = ['rownum' => $row , 'linecontent' => '* <red>'.$date.'</red>* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '.$time.'* '];
            } else {

                if (!preg_match("/^(([0-1]?[0-9])|[2][0-3]|[0-1]):[0-5][0-9]$/", $time)) {
                    $log[] = ['rownum' => $row , 'linecontent' => '* '.$date.'* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* <red>'.$time.'</red>* '];
                } else {

                    $datetimestring = $date . ' ' . $time;
                    $datetime = new \DateTime();
                    $df = $datetime->createFromFormat('m-d-y H:i', $datetimestring);


                    $oldFlight=$this->em->getRepository('AppBundle:Flight')->getFlightByDateAndNumber($num,$datetime->createFromFormat('m-d-y H:i', $datetimestring)->format('Y-m-d'));

                    if(!$oldFlight) {
                        $newflight = new Flight();
                        $newflight->setTime($df);
                        $newflight->setNum($num);
                        $newflight->setFromLocation($from);
                        $newflight->setToLocation($to);
                        $newflight->setCountry($country);
                        $this->em->persist($newflight);
                        $this->em->flush();
                        $newRows[]=['rownum' => $row , 'linecontent' => '* '.$date.'* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '.$time.'* '];

                    }
                    else{
                        $existingRows[]=['rownum' => $row , 'oldLineContent' => '* '.$oldFlight->getTime()->format('Y-m-d H:i').'* '.'* '.$oldFlight->getNum().'* '.'* '.$oldFlight->getFromLocation().'* '.'* '.$oldFlight->getToLocation().'* ' , 'updatedRow' => '* '.$df->format('Y-m-d H:i').'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '];
                        $oldFlight->setTime($df);
                        $oldFlight->setFromLocation($from);
                        $oldFlight->setToLocation($to);
                        $oldFlight->setCountry($country);
                        $this->em->persist($oldFlight);
                        $this->em->flush();
                    }

                    }
            }
            $row++;
        }

        return array($log,$existingRows,$newRows);
    }
    /*
    public function flightImportCSV($file)
    {
        $row=2;

        $log = array();
        $existingRows=array();
        $newRows=array();
        ini_set('max_execution_time', '0');
        $line = fgetcsv($file,null,';');
        while($line = fgetcsv($file,null,';')){
            $date = $line[0];
            $time = $line[4];
            $num = $line[1];
            $from = $line[2];
            $to = $line[3];

            if (!DateTime::createFromFormat('d/m/Y', $date)) {
                $log[] = ['rownum' => $row , 'linecontent' => '* <red>'.$date.'</red>* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '.$time.'* '];
            } else {

                if (!preg_match("/(([0-1][0-9])|[2][0-3]|[0-1]):[0-5][0-9]/", $time)) {
                    $log[] = ['rownum' => $row , 'linecontent' => '* '.$date.'* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* <red>'.$time.'</red>* '];
                } else {

                    $datetimestring = $date . ' ' . $time;
                    $datetime = new \DateTime();
                    $df = $datetime->createFromFormat('d/m/Y H:i', $datetimestring);


                    $oldFlight=$this->em->getRepository('AppBundle:Flight')->getFlightByDateAndNumber($num,$df->format('Y-m-d'));

                    if(!$oldFlight) {
                        $newflight = new Flight();
                        $newflight->setTime($df);
                        $newflight->setNum($num);
                        $newflight->setFromLocation($from);
                        $newflight->setToLocation($to);
                        $this->em->persist($newflight);
                        $this->em->flush();
                        $newRows[]=['rownum' => $row , 'linecontent' => '* '.$date.'* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '.$time.'* '];

                    }
                    else{
                        $existingRows[]=['rownum' => $row , 'oldLineContent' => '* '.$oldFlight->getTime()->format('Y-m-d H:i').'* '.'* '.$oldFlight->getNum().'* '.'* '.$oldFlight->getFromLocation().'* '.'* '.$oldFlight->getToLocation().'* ' , 'updatedRow' => '* '.$df->format('Y-m-d H:i').'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '];
                        $oldFlight->setTime($df);
                        $oldFlight->setFromLocation($from);
                        $oldFlight->setToLocation($to);
                        $this->em->persist($oldFlight);
                        $this->em->flush();
                    }

                }
            }
            $row++;
        }
        return array($log,$existingRows,$newRows);
    }
    public function porteAPorteImportXLSXLSX($file)
    {
        $reader = \PHPExcel_IOFactory::createReaderForFile($file);
        $fileObject = $reader->load($file);
        $sheet = $fileObject->getSheet(0);
        $row = 2;
        $log = array();
        $existingRows=array();
        $newRows=array();
        ini_set('max_execution_time', '0');

        while ($sheet->getCellByColumnAndRow(0, $row) != '') {
            $cp = $sheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
            $location = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
            $price = $sheet->getCellByColumnAndRow(2, $row)->getFormattedValue();
            $agglomeration = $sheet->getCellByColumnAndRow(3, $row)->getFormattedValue();

            if(!preg_match('/^[0-9]+$/',$cp))
            {
                $log[] = ['rownum' => $row , 'linecontent' => '* <red>'.$cp.'</red>* '.'* '.$location.'* '.'* '.$price.'* '.'* '.$agglomeration.'* '];

            }
            else{

            if(preg_match('/[a-zA-Z]/',$price))
                {
                    $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* '.$location.'* '.'* <red>'.$price.'</red>* '.'* '.$agglomeration.'* '];
                }
            else{
                if(preg_match('/[a-zA-Z]/',$agglomeration))
                {
                    $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* '.$location.'* '.'* '.$price.'* '.'* <red>'.$agglomeration.'</red>* '];

                }
                else{
                    $locationDB=$this->em->getRepository('AppBundle:Location')->findOneBy(array('name' => $location));
                       if(!$locationDB)
                       {
                           $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* <red>'.$location.'</red>* '.'* '.$price.'* '.'* '.$agglomeration.'* '];

                       }
                   else {
                       $locationid=$locationDB->getId();
                       $oldRecord = $this->em->getRepository('AppBundle:PorteAPortePrice')->findOneBy(array('zipCode' => $cp, 'location' => $locationid, 'price' => $price, 'agglomeration' => $agglomeration));

                       if ($oldRecord) {
                           $existingRows[]=['rownum' => $row , 'oldLineContent' => '* '.$cp.'* '.'* '.$location.'* '.'* '.$price.'* '.'* '.$agglomeration.'* '  , 'updatedRow' => '---------------'];

                       } else {
                           $agglomerationDB=$this->em->getRepository('AppBundle:Agglomeration')->findOneBy(array('id' => $agglomeration));
                           if($agglomerationDB) {
                               $porteaporte = new PorteAPortePrice();
                               $porteaporte->setAgglomeration($agglomerationDB);
                               $porteaporte->setLocation($locationDB);
                               $porteaporte->setPrice($price);
                               $porteaporte->setZipCode($cp);
                               $this->em->persist($porteaporte);
                               $this->em->flush();
                               $newRows[] = ['rownum' => $row, 'linecontent' => '* ' . $cp . '* ' . '* ' . $location . '* ' . '* ' . $price . '* ' . '* ' . $agglomeration . '* '];;
                            }
                           else{
                               $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* '.$location.'* '.'* '.$price.'* '.'* <red>'.$agglomeration.'</red>* '];

                           }
                       }
                   }
                }



            }
            }

            $row++;
        }

        return array($log,$existingRows,$newRows);

    }
    public function porteAPorteImportCSV($file)
    {
        $row=2;

        $log = array();
        $existingRows=array();
        $newRows=array();
        ini_set('max_execution_time', '0');
        $line = fgetcsv($file,null,';');

        while($line = fgetcsv($file,null,';')){
            $cp = $line[0];
            $location = $line[1];
            $price = $line[2];
            $agglomeration = $line[3];

            if(!preg_match('/^[0-9]+$/',$cp))
            {
                $log[] = ['rownum' => $row , 'linecontent' => '* <red>'.$cp.'</red>* '.'* '.$location.'* '.'* '.$price.'* '.'* '.$agglomeration.'* '];

            }
            else{

                if(preg_match('/[a-zA-Z]/',$price))
                {
                    $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* '.$location.'* '.'* <red>'.$price.'</red>* '.'* '.$agglomeration.'* '];
                }
                else{
                    if(preg_match('/[a-zA-Z]/',$agglomeration))
                    {
                        $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* '.$location.'* '.'* '.$price.'* '.'* <red>'.$agglomeration.'</red>* '];

                    }
                    else{
                        $locationDB=$this->em->getRepository('AppBundle:Location')->findOneBy(array('name' => $location));
                        if(!$locationDB)
                        {
                            $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* <red>'.utf8_encode($location).'</red>* '.'* '.$price.'* '.'* '.$agglomeration.'* '];

                        }
                        else {
                            $locationid=$locationDB->getId();
                            $oldRecord = $this->em->getRepository('AppBundle:PorteAPortePrice')->findOneBy(array('zipCode' => $cp, 'location' => $locationid, 'price' => $price, 'agglomeration' => $agglomeration));

                            if ($oldRecord) {
                                $existingRows[]=['rownum' => $row , 'oldLineContent' => '* '.$cp.'* '.'* '.$location.'* '.'* '.$price.'* '.'* '.$agglomeration.'* '  , 'updatedRow' => '-----------------------'];

                            } else {
                                $agglomerationDB=$this->em->getRepository('AppBundle:Agglomeration')->findOneBy(array('id' => $agglomeration));
                                if($agglomerationDB) {
                                    $porteaporte = new PorteAPortePrice();
                                    $porteaporte->setAgglomeration($agglomerationDB);
                                    $porteaporte->setLocation($locationDB);
                                    $porteaporte->setPrice($price);
                                    $porteaporte->setZipCode($cp);
                                    $this->em->persist($porteaporte);
                                    $this->em->flush();
                                    $newRows[] = ['rownum' => $row, 'linecontent' => '* ' . $cp . '* ' . '* ' . $location . '* ' . '* ' . $price . '* ' . '* ' . $agglomeration . '* '];;
                                }
                                else{
                                    $log[] = ['rownum' => $row , 'linecontent' => '*'.$cp.'* '.'* '.$location.'* '.'* '.$price.'* '.'* <red>'.$agglomeration.'</red>* '];

                                }
                            }
                        }
                    }



                }
            }

            $row++;
        }
        return array($log,$existingRows,$newRows);
        }
    public function intervilleImportXLSXLSX($file)
    {

        $reader = \PHPExcel_IOFactory::createReaderForFile($file);
        $fileObject = $reader->load($file);
        $sheet = $fileObject->getSheet(0);
        $row = 2;
        $log = array();
        $existingRows=array();
        $newRows=array();
        ini_set('max_execution_time', '0');
        while ($sheet->getCellByColumnAndRow(0, $row) != '') {
            $date = $sheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
            $time = $sheet->getCellByColumnAndRow(4, $row)->getFormattedValue();
            $num = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
            $from = $sheet->getCellByColumnAndRow(2, $row)->getFormattedValue();
            $to = $sheet->getCellByColumnAndRow(3, $row)->getFormattedValue();

            if (!DateTime::createFromFormat('m-d-y', $date)) {
                $log[] = ['rownum' => $row , 'linecontent' => '* <red>'.$date.'</red>* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '.$time.'* '];
            } else {

                if (!preg_match("/^(([0-1][0-9])|[2][0-3]|[0-1]):[0-5][0-9]$/", $time)) {
                    $log[] = ['rownum' => $row , 'linecontent' => '* '.$date.'* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* <red>'.$time.'</red>* '];
                } else {

                    $datetimestring = $date . ' ' . $time;
                    $datetime = new \DateTime();
                    $df = $datetime->createFromFormat('m-d-y H:i', $datetimestring);


                    $oldFlight=$this->em->getRepository('AppBundle:Flight')->getFlightByDateAndNumber($num,$datetime->createFromFormat('m-d-y H:i', $datetimestring)->format('Y-m-d'));

                    if(!$oldFlight) {
                        $newflight = new Flight();
                        $newflight->setTime($df);
                        $newflight->setNum($num);
                        $newflight->setFromLocation($from);
                        $newflight->setToLocation($to);
                        $this->em->persist($newflight);
                        $this->em->flush();
                        $newRows[]=['rownum' => $row , 'linecontent' => '* '.$date.'* '.'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '.$time.'* '];

                    }
                    else{
                        $existingRows[]=['rownum' => $row , 'oldLineContent' => '* '.$oldFlight->getTime()->format('Y-m-d H:i').'* '.'* '.$oldFlight->getNum().'* '.'* '.$oldFlight->getFromLocation().'* '.'* '.$oldFlight->getToLocation().'* ' , 'updatedRow' => '* '.$df->format('Y-m-d H:i').'* '.$num.'* '.'* '.$from.'* '.'* '.$to.'* '.'* '];
                        $oldFlight->setTime($df);
                        $oldFlight->setFromLocation($from);
                        $oldFlight->setToLocation($to);
                        $this->em->persist($oldFlight);
                        $this->em->flush();
                    }

                }
            }
            $row++;
        }

        return array($log,$existingRows,$newRows);
    }
*/
}