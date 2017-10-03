<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Util\Escape;

/**
 * EBSR variation number
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EbsrVariationNumber implements FormatterInterface
{
    const SN_TRANSLATION_KEY = 'ebsr-variation-short-notice';

    /**
     * Formats the ebsr variation number
     *
     * @param array                        $data   data array
     * @param array                        $column column info
     * @param null|ServiceLocatorInterface $sm     service locator
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        /**
         * far from ideal, but we sometimes get data in different formats as follows:
         *
         * 1. if it's from BusRegSearchView entity it's a flat array
         * 2. if it's from anywhere else, it's in the $data['busReg'] array key
         */
        if (isset($data['busReg'])) {
            $data = $data['busReg'];
        }

        //if no variation number return empty string
        if (!isset($data['variationNo'])) {
            return '';
        }

        $variationNo = Escape::html($data['variationNo']);

        //if the record is short notice, add a short notice status flag
        if ($data['isShortNotice'] === 'Y') {
            /** @var \Common\View\Helper\Status $statusHelper */
            $statusHelper = $sm->get('ViewHelperManager')->get('status');

            $status = [
                'colour' => 'orange',
                'value' => $sm->get('translator')->translate(self::SN_TRANSLATION_KEY)
            ];

            return $variationNo . $statusHelper->__invoke($status);
        }

        //not short notice, so return the variation number by itself
        return $variationNo;
    }
}
