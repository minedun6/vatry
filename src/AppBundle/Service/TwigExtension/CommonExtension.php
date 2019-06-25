<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 24/04/2016
 * Time: 15:58
 */

namespace AppBundle\Service\TwigExtension;


class CommonExtension extends \Twig_Extension {

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('country', array($this, 'countryFilter')),
            new \Twig_SimpleFilter('round', array($this, 'roundFilter')),
        );
    }

    public function countryFilter($countryCode,$locale = "en"){
        $c = \Symfony\Component\Locale\Locale::getDisplayCountries("en");

        return array_key_exists($countryCode, $c)
            ? $c[$countryCode]
            : $countryCode;
    }

    public function roundFilter($number){
        return round($number, 2);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'common_extension';
    }
}