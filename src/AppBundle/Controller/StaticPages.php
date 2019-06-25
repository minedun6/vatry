<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 22/05/2016
 * Time: 23:23
 */

namespace AppBundle\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use stdClass;
use AppBundle\Entity\AgencePartner;

class StaticPages extends Controller {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/nos-partenaires",name="nos_partenaires")
     */
    public function partners(){

      $partneragency=[];
      $relaycustomer=[];

      $repository = $this->getDoctrine()->getRepository('AppBundle:AgencePartner');
      $agencepartners=$repository->findBy(array('status' => true));

      $relayclients = $this->getDoctrine()
          ->getRepository("AppBundle:User")
          ->findByType('relayCustomer');



      if($relayclients)
      {
      foreach($relayclients as $relayclient)
      {
           if($relayclient->getRelayCustomerDetail()->getType()=="agency")
            {
              $client = new StdClass();
              $nom=$relayclient->getRelayCustomerDetail()->getCorporateName();
              $ville=$relayclient->getPerson()->getTown();
              $cp=$relayclient->getPerson()->getZipCode();
              $phone=$relayclient->getPerson()->getTel();
              $email=$relayclient->getPerson()->getEmail();

              $client->nom=$nom;
              $client->ville=$ville;
              $client->cp=$cp;
              $client->phone=$phone;
              $client->email=$email;

              $relaycustomer[]=$client;
              unset($client);
            }
      }
    }


      if($agencepartners)
      {
      foreach ($agencepartners as $agencepartner) {
         $agence = new StdClass();
        $nom=$agencepartner->getAgence()->getNom();
        $ville=$agencepartner->getAgence()->getVille();
        $cp=$agencepartner->getAgence()->getCp();
        $phone=$agencepartner->getAgence()->getTel();
        $email=$agencepartner->getAgence()->getEmail();

        $agence->nom=$nom;
        $agence->ville=$ville;
        $agence->cp=$cp;
        $agence->phone=$phone;
        $agence->email=$email;

        $partneragency[]=$agence;
        unset($agence);

      }

      return $this->render("@App/static_pages/partners.html.twig",array('partneragency'=>$partneragency,'relaycustomer' => $relaycustomer));
    }


      return $this->render("@App/static_pages/partners.html.twig",array('partneragency'=>$partneragency,'relaycustomer' => $relaycustomer));

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/contact",name="contact")
     */
    public function contact(){
        return $this->render("@App/static_pages/contact.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/qui-sommes-nous",name="about_us")
     */
    public function aboutUS(){
        return $this->render("@App/static_pages/about_us.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/reservations-navettes-aeroport-vatry",name="reservations-navettes-aeroport-vatry")
     */
    public function generalReservation(){
        return $this->render("@App/static_pages/generalReservation.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/cgv",name="cgv")
     */
    public function cgv(){
        return $this->render("@App/static_pages/cgv.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/mentions_legale",name="mentions_legale")
     */
    public function mentions_legale(){
        return $this->render("@App/static_pages/mentions_legale.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/destinations_gares",name="destinations_gares")
     */
    public function destinationGares(){
        return $this->render("@App/static_pages/destinations_gares.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/destinations_privates",name="destinations_privates")
     */
    public function destinationPrivates(){
        return $this->render("@App/static_pages/destinations_privates.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/navette-partagee-inter-villes",name="description_interville")
     */
    public function descriptionInterVille(){
        return $this->render("@App/static_pages/description_interville.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfert-aeroport-domicile",name="description_porteaporte")
     */
    public function descriptionPorteAporte(){
        return $this->render("@App/static_pages/description_porteaporte.html.twig");
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/description_private",name="description_private")
     */
    public function descriptionPrivate(){
        return $this->render("@App/static_pages/description_private.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/description_privateaeroport",name="description_privateaeroport")
     */
    public function descriptionPrivateAeroport(){
        return $this->render("@App/static_pages/description_privateaeroport.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/description_lastminute",name="description_lastminute")
     */
    public function descriptionLastMinute(){
        return $this->render("@App/static_pages/description_lastminute.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/description_garereims",name="description_garereims")
     */
    public function descriptionReims(){
        return $this->render("@App/static_pages/description_reims.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/description_garechalon",name="description_garechalon")
     */
    public function descriptionChalon(){
        return $this->render("@App/static_pages/description_chalon.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/index2",name="index2")
     */
    public function index2(){

        return $this->render("::base07062016.html.twig");
    }



    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/vols-aeroport-vatry",name="volsTempsReel")
     */
    public function volsTempsReels(){

        return $this->render("@App/static_pages/vols_temp_reels.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/villes-desservies-aeroport-vatry",name="villes_desservies")
     */
    public function villesDesservies(){

        return $this->render("@App/static_pages/villes_desservies.twig");
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfert-prive-aeroport-aeroport",name="t_aeroport")
     */
    public function aeroport(){

        return $this->render("@App/static_pages/aeroport.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/map_gare",name="mapgare")
     */
    public function mapGare(){

        return $this->render("@App/front/gareTransfer/villes_desservies_list.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/map_aeroport",name="mapaeroport")
     */
    public function mapAeroport(){

        return $this->render("@App/static_pages/aeroport_villes_desservies_list.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfert-prive-aeroport-domicile",name="t_domicile")
     */
    public function domicile(){

        return $this->render("@App/static_pages/domicile.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/champagne_destination",name="champagne_destination")
     */
    public function champagne_destination(){

        return $this->render("@App/static_pages/champagne-destination.html.twig");
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/interville_destination",name="interville_destination")
     */
    public function interville_destination(){

        return $this->render("@App/static_pages/interville_destination.html.twig");
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/navette_partage",name="navette_partage")
     */
    public function navette_partage(){

        return $this->render("@App/static_pages/navette-partage.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/region_paris_destination",name="region_paris_destination")
     */
    public function region_paris_destination(){

        return $this->render("@App/static_pages/region_paris_destination.html.twig");
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/reims_destination",name="reims_destination")
     */
    public function reims_destination(){

        return $this->render("@App/static_pages/reims_destination.html.twig");
    }



    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/taxi_aeroport_aeroport",name="taxi_aeroport_aeroport")
     */
    public function taxi_aeroport_aeroport(){

        return $this->render("@App/static_pages/taxi_aeroport_aeroport.html.twig");
    }



    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/taxi_aeroport_domicile",name="taxi_aeroport_domicile")
     */
    public function taxi_aeroport_domicile(){

        return $this->render("@App/static_pages/taxi_aeroport_domicile.html.twig");
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/navette-partagee-aeroport-paris",name="region_paris")
     */
    public function region_paris(){

        return $this->render("@App/static_pages/region-paris.html.twig");
    }


}
