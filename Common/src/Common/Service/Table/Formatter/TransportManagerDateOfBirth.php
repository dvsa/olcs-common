<?php

namespace Common\Service\Table\Formatter;

use Laminas\View\HelperPluginManager;

class TransportManagerDateOfBirth extends Date
{
    private HelperPluginManager $viewHelperManager;

    public function __construct(HelperPluginManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;
    }
    /**
     * {@inheritdoc}
     */
    public function format($data, $column = [])
    {
        $dob = parent::format($data, $column);

        if (self::shouldShowStatus($column)) {
            return sprintf('<span class="nowrap">%s %s</span>', $dob, self::getStatusHtml($data));
        }

        return $dob;
    }

    /**
     * Whether the status should be displayed after the date of birth
     *
     * @param array $column
     *
     * @return bool
     */
    protected function shouldShowStatus(array $column)
    {
        if (!isset($column['internal']) || (!isset($column['lva']))) {
            return false;
        }

        if ($column['lva'] == 'variation' || $column['lva'] == 'application') {
            return true;
        }

        return false;
    }

    /**
     * Get the html for the status
     *
     * @param array $data Row Data
     *
     * @return string HTML
     */
    protected function getStatusHtml(array $data)
    {
        $viewHelper = $this->viewHelperManager->get('transportManagerApplicationStatus');

        $id = $data['status']['id'] ?? '';
        $description = $data['status']['description'] ?? '';

        return $viewHelper->render($id, $description);
    }
}
