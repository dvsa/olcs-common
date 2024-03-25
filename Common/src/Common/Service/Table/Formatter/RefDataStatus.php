<?php

namespace Common\Service\Table\Formatter;

use Laminas\View\HelperPluginManager;

/**
 * Fetches the refdata and then adds status formatting
 */
class RefDataStatus implements FormatterPluginManagerInterface
{
    protected $viewHelperManager;

    private RefData $refDataFormatter;

    public function __construct(HelperPluginManager $viewHelperManager, RefData $refDataFormatter)
    {
        $this->viewHelperManager = $viewHelperManager;
        $this->refDataFormatter = $refDataFormatter;
    }

    /**
     * Format a address
     *
     * @param array $data   Row data
     * @param array $column Column params
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $description = $this->refDataFormatter->format($data, $column);

        $status = [
            'id' => $data[$column['name']]['id'],
            'description' => $description
        ];

        /**
         * @var \Common\View\Helper\Status $statusHelper
        */
        $statusHelper = $this->viewHelperManager->get('status');

        return $statusHelper->__invoke($status);
    }
}
