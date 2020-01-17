<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\Helper\UrlHelperService;
use Common\View\Helper\Status as StatusHelper;

/**
 * Bus reg number link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegNumberLink implements FormatterInterface
{
    const LINK_PATTERN = '<a href="%s">%s</a>';
    const URL_ROUTE = 'licence/bus-details/service'; //internal bus reg service details page
    const LABEL_TRANSLATION_KEY = 'ebsr-link-label';
    const LABEL_COLOUR = 'orange';

    /**
     * Formats the bus registration number with optional EBSR label
     *
     * @param array                        $data   data array
     * @param array                        $column column info
     * @param null|ServiceLocatorInterface $sm     service locator
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $outputStatus = '';

        //if app is EBSR add a label
        if ($data['isTxcApp']) {
            /** @var Translator $translator */
            $translator = $sm->get('translator');

            $status = [
                'colour' => self::LABEL_COLOUR,
                'value' => $translator->translate(self::LABEL_TRANSLATION_KEY),
            ];

            /** @var StatusHelper $statusHelper */
            $statusHelper = $sm->get('ViewHelperManager')->get('status');
            $outputStatus = $statusHelper->__invoke($status);
        }

        /** @var UrlHelperService $urlHelper */
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(self::URL_ROUTE, ['busRegId' => $data['id']], [], true);

        return sprintf(self::LINK_PATTERN, $url, Escape::html($data['regNo'])) . $outputStatus;
    }
}
