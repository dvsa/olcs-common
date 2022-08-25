<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EbsrDocumentStatus implements FormatterInterface
{
    /**
     * Formats the status of an EBSR document
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
         * @todo
         *
         * Once the EBSR status data has been cleansed, this can be simplified and moved to the
         * Common\View\Helper\Status helper
         */
        switch ($data['ebsrSubmissionStatus']['id']) {
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

        return $statusHelper->__invoke($status);
    }
}
