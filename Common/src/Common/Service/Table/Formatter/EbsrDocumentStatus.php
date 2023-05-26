<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Laminas\View\HelperPluginManager;

class EbsrDocumentStatus implements FormatterPluginManagerInterface
{
    private HelperPluginManager $viewHelperManager;

    public function __construct(HelperPluginManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;
    }

    /**
     * Formats the status of an EBSR document
     *
     * @param array $data   data array
     * @param array $column column info
     *
     * @return string
     */
    public function format($data, $column = [])
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

        /**
        * @var \Common\View\Helper\Status $statusHelper
        */
        $statusHelper = $this->viewHelperManager->get('status');

        return $statusHelper->__invoke($status);
    }
}
