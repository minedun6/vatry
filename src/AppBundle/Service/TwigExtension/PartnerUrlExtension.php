<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 19/06/2016
 * Time: 13:14
 */

namespace AppBundle\Service\TwigExtension;


use AppBundle\Entity\Partner;
use AppBundle\Service\Back\PartnerService;

class PartnerUrlExtension extends \Twig_Extension {

    private $partnerService;

    public function __construct(PartnerService $partnerService){
        $this->partnerService = $partnerService;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('getUrl', array($this, 'showUrlFilter')),
        );
    }

    public function showUrlFilter(Partner $partner){
        $url = $this->partnerService->getUrlForPartner($partner);
        if ($url == null){
            return '-';
        }else{
            return $url;
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'partner_extension';
    }

}