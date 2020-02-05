<?php

namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Guides Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @method \Olcs\Mvc\Controller\Plugin\Placeholder placeholder()
 */
class GuidesController extends ZendAbstractActionController
{
    const GUIDE_OC_ADV_GB_NEW = 'advertising-your-operating-centre-gb-new';
    const GUIDE_OC_ADV_GB_VAR = 'advertising-your-operating-centre-gb-var';
    const GUIDE_OC_ADV_NI_NEW = 'advertising-your-operating-centre-ni-new';
    const GUIDE_OC_ADV_NI_VAR = 'advertising-your-operating-centre-ni-var';
    const GUIDE_PRIVACY_NOTICE = 'privacy-notice';
    const GUIDE_TERMS_AND_CONDITIONS = 'terms-and-conditions';
    const GUIDE_FINANCIAL_EVIDENCE = 'financial-evidence';
    const GUIDE_TRAFFIC_AREA = 'traffic-area';
    const GUIDE_CONVICTIONS_AND_PENALTIES_GUIDANCE_GB = 'convictions-and-penalties-guidance-gb';
    const GUIDE_CONVICTIONS_AND_PENALTIES_GUIDANCE_NI = 'convictions-and-penalties-guidance-ni';

    protected $guideMap = [
        self::GUIDE_OC_ADV_GB_NEW => 'oc_advert',
        self::GUIDE_OC_ADV_GB_VAR => 'oc_advert',
        self::GUIDE_OC_ADV_NI_NEW => 'oc_advert',
        self::GUIDE_OC_ADV_NI_VAR => 'oc_advert',
        self::GUIDE_PRIVACY_NOTICE => 'default',
        self::GUIDE_TERMS_AND_CONDITIONS => 'default',
        self::GUIDE_FINANCIAL_EVIDENCE => 'default',
        self::GUIDE_TRAFFIC_AREA => 'default',
        self::GUIDE_CONVICTIONS_AND_PENALTIES_GUIDANCE_GB => 'default',
        self::GUIDE_CONVICTIONS_AND_PENALTIES_GUIDANCE_NI => 'default',
    ];

    public function indexAction()
    {
        $guide = (string)$this->params('guide');

        if (!isset($this->guideMap[$guide])) {
            return $this->notFoundAction();
        }

        $partial = $this->guideMap[$guide];

        $view = new ViewModel(['guide' => $guide]);
        $view->setTemplate('pages/guides/' . $partial);

        $this->placeholder()->setPlaceholder('pageTitle', $guide . '-title');

        return $view;
    }
}
