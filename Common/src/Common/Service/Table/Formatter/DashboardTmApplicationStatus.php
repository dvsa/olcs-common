<?php

namespace Common\Service\Table\Formatter;

use Laminas\View\HelperPluginManager;

class DashboardTmApplicationStatus implements FormatterPluginManagerInterface
{
    private HelperPluginManager $viewHelperManager;

    public function __construct(HelperPluginManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;
    }
    /**
     * Generate the HTML to display the TM Application status
     *
     * @param  array $data
     * @param  array $column
     * @return string HTML
     */
    public function format($data, $column = [])
    {
        $viewHelper = $this->viewHelperManager->get('transportManagerApplicationStatus');

        return
            $viewHelper->render(
                $data['transportManagerApplicationStatus']['id'],
                $data['transportManagerApplicationStatus']['description']
            );
    }
}
