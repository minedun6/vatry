<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 27/04/2016
 * Time: 21:33
 */

namespace AppBundle\Utilities;


class Helpers
{

    public static function generateRandomString($n = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $n; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    public static function createDateTimeByMinutes(\DateTime $date, $minutes)
    {

        $minutes += intval($date->format('i'));
        $minutes += 60 * intval($date->format('H'));
        $m = $minutes % 60;
        $h = ($minutes - $m) / 60;

        $ts = mktime(
            $h, $m, 0,
            intval($date->format('m')),
            intval($date->format('d')),
            intval($date->format('Y'))
        );

        $newDate = new \DateTime();
        $newDate->setTimestamp($ts);

        return $newDate;
    }

    public static function createDateTimeByMinutesV2(\DateTime $date, $minutes)
    {

        $seconds = ($minutes * 60) ;

        $ts = $date->getTimestamp() + $seconds ;

        $newDate = new \DateTime();
        $newDate->setTimestamp($ts);

        return $newDate;
    }

    public static function issetOrNull($tab,$key){
        if (isset($tab[$key])){
            return $tab[$key];
        }
        return null;
    }

    public static function convertDateTimeToDbDate($dateTime){
        $dateTab = explode('/',$dateTime);
        $date = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
        return $date;
    }

}