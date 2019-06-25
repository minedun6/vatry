<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 22/06/2016
 * Time: 14:59
 */

namespace AppBundle\Controller\Back;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Console\Input\Input;
use AppBundle\Service\Back\DataBaseFileImportService;


/**
 * @Route("/admin")
 */
class ParametrageController extends Controller
{
    /**
     * @Route("/parametrage/{variable}",name="parametrage_pathvariable")
     */
    public function index($variable)
    {
        return $this->render("@App/Back/DBParametrage/parametrage_main.html.twig", array('requestedPage' => $variable));
    }

    /**
     * @Route("/parametrage",name="parametrage_mainpage")
     */

    public function mainPage()
    {
        return $this->render("@App/Back/DBParametrage/parametrage_main.html.twig", array('requestedPage' => 'main'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/data_zone/",name="data_list",options={"expose"=true})
     */
    public function generateZoneAction($page)
    {
        switch ($page) {
            case 'vols': {
                $headers = ['id', 'Num', 'De', 'Vers', 'Date/heure', 'Pays'];
                $content = $this->getDoctrine()
                    ->getRepository('AppBundle:Flight')
                    ->createQueryBuilder('e')
                    ->select('e')
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                return $this->render("@App/Back/DBParametrage/table_component.html.twig", array('headers' => $headers, 'content' => $content, 'jsvar' => 'flight'));

            }
            case 'agglomeration': {
                $headers = ['id', 'Nom', 'lat', 'lng'];
                $content = $this->getDoctrine()
                    ->getRepository('AppBundle:Agglomeration')
                    ->createQueryBuilder('e')
                    ->select('e')
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                return $this->render("@App/Back/DBParametrage/table_component.html.twig", array('headers' => $headers, 'content' => $content, 'jsvar' => 'agglomeration'));

            }
            case 'location': {
                $headers = ['id', 'Nom', 'Zip code', 'Type', 'Lat', 'Lng'];
                return $this->render("@App/Back/DBParametrage/bigtable_component.html.twig", array('headers' => $headers, 'content' => '', 'jsvar' => 'location', 'datasource' => 'location_datasource'));

            }
            case 'privatelocation': {
                $headers = ['ID', 'location', 'Prix', 'Capacité min', 'Capacité max', 'Code ZIP', 'Distance'];
                return $this->render("@App/Back/DBParametrage/bigtable_component.html.twig", array('headers' => $headers, 'content' => '', 'jsvar' => 'privatelocation', 'datasource' => 'privatelocation_datasource'));
            }
            case 'porteaporte': {
                $headers = ['ID', 'Location', 'agglomeration', 'Code ZIP', 'Prix' , 'Prix Agence'];
                $content = $this->getDoctrine()
                    ->getRepository('AppBundle:PorteAPortePrice')
                    ->createQueryBuilder('e')
                    ->join('e.agglomeration', 'a')
                    ->join('e.location', 'l')
                    ->select('e,l.name as nm ,a.name as nn')
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
//                var_dump($content);
//                die(0);
                return $this->render("@App/Back/DBParametrage/table_component.html.twig", array('headers' => $headers, 'content' => $content, 'jsvar' => 'porteaporte'));
            }
            case 'interville': {
                $headers = ['ID', 'Location', 'Code ZIP', 'RDV', 'Prix', 'Prix agence', 'Durée'];
                $content = $this->getDoctrine()
                    ->getRepository('AppBundle:InterVillePrices')
                    ->createQueryBuilder('e')
                    ->join('e.location', 'l')
//                ->select('e.id,l.name,e.zipCode,e.rdv,e.adultePrice,e.childPrice,e.babyPrice,e.duration')
                 ->select('e,l.name')
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                return $this->render("@App/Back/DBParametrage/table_component.html.twig", array('headers' => $headers, 'content' => $content, 'jsvar' => 'interville'));
            }
            default:
                return $this->render("@App/Back/DBParametrage/recap.html.twig", array(
                    'vol' => $this->getDoctrine()->getRepository("AppBundle:Flight")->getCount(),
                    'interville' => $this->getDoctrine()->getRepository("AppBundle:InterVillePrices")->getCount(),
                    'agglomeration' => $this->getDoctrine()->getRepository("AppBundle:Agglomeration")->getCount(),
                    'location' => $this->getDoctrine()->getRepository("AppBundle:Location")->getCount(),
                    'porteAporte' => $this->getDoctrine()->getRepository("AppBundle:PorteAPortePrice")->getCount(),
                    'privatelocation' => $this->getDoctrine()->getRepository("AppBundle:PrivateLocationPrice")->getCount()));

        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/import_fileToDB",name="import_file_database")
     */

    public function uploadFileToDatabase(Request $request)
    {   $file=$request->files->get('imported');
        $ext=$file->getClientOriginalExtension();
        $data=null;
        $target=$request->get('target');

        if($ext=='xls'|| $ext=='xlsx') {

            switch($target){
            case 'flight':   {$data = $this->get('database.file.import.service')->flightImportXLSXLSX($file);break;}
            case 'porteaporte': {$data=$this->get('database.file.import.service')->porteAPorteImportXLSXLSX($file);break;}
            default:break;}
        }
        else if ($ext=='csv')
        {
            $csvRessource=fopen($file,'r');
            switch($target){
            case 'flight':   {$data=$this->get('database.file.import.service')->flightImportCSV($csvRessource);break;}
            case 'porteaporte': {$data=$this->get('database.file.import.service')->porteAPorteImportcsv($csvRessource);break;}
            default:break;}
        }
        else
        {
            return new JsonResponse(array(),500);
        }
        $log = $data[0];
        $existingRows = $data[1];
        $newRows=$data[2];
        return new JsonResponse(array('log' => $log, 'existing' => $existingRows,'newrows' => $newRows));
    }




}