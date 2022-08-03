<?php

namespace Common\Service\Table\Formatter;

class DashboardTmApplicationStatus implements FormatterInterface
{
    /**
     * Generate the HTML to display the TM Application status
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string HTML
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $viewHelper = $sm->get('ViewHelperManager')->get('transportManagerApplicationStatus');

        return
            $viewHelper->render(
                $data['transportManagerApplicationStatus']['id'],
                $data['transportManagerApplicationStatus']['description']
            );
    }
}
