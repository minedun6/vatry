<?php

namespace AppBundle\Service\Front\Flights;

class ServiceInfosFlight
{
    public function ConnectApi($id)
    {
        $options = array(
            'trace' => true,
            'exceptions' => 0,
            'login' => 'NavetteVatry',
            'password' => 'b38f6411b7850d1142d163af5ba6db3d8fd3ef88',
        );

        $client = new \SoapClient('http://flightaware.com/commercial/flightxml/data/wsdl1.xml', $options);

        if (empty($id)) {
            throw new \Exception('Id de l\'avion manquant !');
        } else {
            $result = $client->InFlightInfo($id);
        }


        $infos = array(
            'ident' => $result->ident,
            'latitude' => $result->latitude,
            'longitude' => $result->longitude,
            'altitude' => $result->altitude,
            'firstPositionTime' => $result->firstPositionTime,
            'altitudeStatus' => $result->altitudeStatus,
            'groundspeed' => $result->groundspeed,
            'timestamp' => $result->timestamp);

        return $infos;
    }

}

?>
