<?php

namespace AppBundle\Service\Front\Flights;

use Symfony\Component\Security\Acl\Exception\Exception;

class FlightService
{
    public function connectApi()
    {
        $options = array(
            'trace' => true,
            'exceptions' => 0,
            'login' => 'NavetteVatry',
            'password' => 'b38f6411b7850d1142d163af5ba6db3d8fd3ef88',
        );

        $client = new \SoapClient('http://flightxml.flightaware.com/soap/FlightXML2/wsdl', $options);
        return $client;
    } //Se connecter à l'API

   public function getTrackLog($volnum)
    {
        $client = $this->connectApi();
        $params = array("ident" => $volnum);
        $result=$client->GetLastTrack($params);
        return $result->GetLastTrackResult->data;
    } // Retourne le chemin parcouru par l'avion

    public function flightStatus($volnumber)
    {
        $client = $this->connectApi();
        $query=$client->InFlightInfo(array("ident" => $volnumber));
        $result=$query->InFlightInfoResult;

            $infos = array(
                'ident' => $result->ident,
                'latitude' => $result->latitude,
                'longitude' => $result->longitude,
                'origin' => $result->origin,
                'destination' => $result->destination
            );

        return $infos;
    } //Retourne la position d'un avion.

    public function getFlight($date,$volnum)
    {
        $client = $this->connectApi();
        $params = array("ident" => $volnum, "howMany" => 15, "offset" => 0);
        $Queryresult=$client->FlightInfoEx($params);

        if(property_exists ( $Queryresult, 'faultstring' ))
        {
            return null;
        }
        $result=$Queryresult->FlightInfoExResult;
        $flights=$result->flights;

        if($flights){
            if(gettype($flights)=='object')
            {
                $d = date('d/m/Y', $flights->filed_departuretime);


                if($d==$date)
                {
                    return $flights;
                }
            }
            else {
                foreach ($flights as $flight) {
                    $d = date('d/m/Y', $flight->filed_departuretime);


                    if ($d == $date) {
                        return $flight;
                    }
                }
            }
        }
        return null;
    } // Retourne les details d'un vol.

    public function getAirportInfo($airportCode)
    {
        $client = $this->connectApi();
        $params = array("airportCode" => $airportCode);
        $QueryResult = $client->AirportInfo($params);
        if(property_exists ( $QueryResult, 'faultstring' ))
        {
            return null;
        }
        $QueryResult->AirportInfoResult->timezone=ltrim($QueryResult->AirportInfoResult->timezone,':');
        return $QueryResult->AirportInfoResult;

    }

    public function getFlightDetails($flight,$date)
    {
            $flightapi=$this->getFlight($date,$flight->getNum());
            $actual_departuretime = '-';
            $estimated_arrival = '-';
            $status='-';
            if($flightapi) {
               if ($flightapi->actualdeparturetime != 0)
                {
                    //$actual_departuretime = date('H:i',$flightapi->actualdeparturetime);
                    $ad=new \DateTime();
                    $ad->setTimestamp($flightapi->actualdeparturetime);
                    $timezone=($this->getAirportInfo($flightapi->origin))?$this->getAirportInfo($flightapi->origin)->timezone:date_default_timezone_get();
                    $ad->setTimezone(new \DateTimeZone($timezone));
                    $actual_departuretime = $ad->format('H:i');

                }
                else{
                    //$actual_departuretime = date('H:i',$flightapi->filed_departuretime);
                    $ad=new \DateTime();
                    $ad->setTimestamp($flightapi->filed_departuretime);
                    $timezone=($this->getAirportInfo($flightapi->origin))?$this->getAirportInfo($flightapi->origin)->timezone:date_default_timezone_get();
                    $ad->setTimezone(new \DateTimeZone($timezone));
                    $actual_departuretime = $ad->format('H:i');
                }
                if ($flightapi->estimatedarrivaltime != 0)
                {
                    //$estimated_arrival = date('H:i',$flightapi->estimatedarrivaltime);
                    $ad=new \DateTime();
                    $ad->setTimestamp($flightapi->estimatedarrivaltime);
                    $timezone=($this->getAirportInfo($flightapi->destination))?$this->getAirportInfo($flightapi->destination)->timezone:date_default_timezone_get();
                    $ad->setTimezone(new \DateTimeZone($timezone));
                    $estimated_arrival = $ad->format('H:i');
                }
                if($flightapi->actualarrivaltime!=0)
                {
                    $status='Attéri';
                }
                else if($flightapi->actualarrivaltime==0&&$flightapi->actualdeparturetime!=0)
                {
                    $status='En vol';

                }
                else
                {
                    $status='Prévue';
                }
            }
            $infos = array (
                'num' => $flight->getNum(),
                'from' => $flight->getFromLocation(),
                'to' => $flight->getToLocation(),
                'departprogrammee' => $actual_departuretime,
                'arriveestimee' => $estimated_arrival,
                'status' => $status,
            );
            return $infos;

        } // On étend la methode getFlight()


}

?>
