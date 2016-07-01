<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;

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
        //if no variation number return empty string
        if (!isset($data['busReg']['variationNo'])) {
            return '';
        }

        //if the record is short notice, add a short notice status flag
        if ($data['busReg']['isShortNotice'] === 'Y') {
            /** @var \Common\View\Helper\Status $statusHelper */
            $statusHelper = $sm->get('ViewHelperManager')->get('status');

            $status = [
                'colour' => 'orange',
                'value' => $sm->get('translator')->translate(self::SN_TRANSLATION_KEY)
            ];

            return $data['busReg']['variationNo'] . $statusHelper->__invoke($status);
        }

        //not short notice, so return the variation number by itself
        return $data['busReg']['variationNo'];
    }
}
