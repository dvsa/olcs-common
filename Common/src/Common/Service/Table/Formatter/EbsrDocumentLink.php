<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * EBSR document link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EbsrDocumentLink implements FormatterInterface
{
    const LINK_PATTERN = '<a class="file__link" href="%s">%s %s</a>';
    const URL_ROUTE = 'bus-registration/ebsr';
    const URL_ACTION = 'detail';

    /**
     * Formats the link to an EBSR document
     *
     * @param array                        $data   data array
     * @param array                        $column column info
     * @param null|ServiceLocatorInterface $sm     service locator
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        /** @var \Common\Service\Helper\UrlHelperService $statusHelper */
        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            self::URL_ROUTE,
            [
                'id' => $data['id'],
                'action' => self::URL_ACTION
            ]
        );

        /**
         * @todo
         *
         * Once the EBSR status data has been cleansed, this can be simplified and moved to the
         * Common\View\Helper\Status helper
         */
        switch($data['ebsrSubmissionStatus']['id']) {
            case RefData::EBSR_STATUS_PROCESSING:
            case RefData::EBSR_STATUS_VALIDATING:
            case RefData::EBSR_STATUS_SUBMITTED:
                $status = [
                    'colour' => 'orange',
                    'value' => 'processing'
                ];
                break;
            case RefData::EBSR_STATUS_PROCESSED:
                $status = [
                    'colour' => 'green',
                    'value' => 'successful'
                ];
                break;
            default:
                $status = [
                    'colour' => 'red',
                    'value' => 'failed'
                ];
        }

        /** @var \Common\View\Helper\Status $statusHelper */
        $statusHelper = $sm->get('ViewHelperManager')->get('status');

        return sprintf(self::LINK_PATTERN, $url, $data['document']['description'], $statusHelper->__invoke($status)); 
    }
}
