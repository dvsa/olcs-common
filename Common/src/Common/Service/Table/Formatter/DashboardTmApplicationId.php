<?php

/**
 * Dashboard Transport Manager Application ID
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Dashboard Transport Manager Application ID
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DashboardTmApplicationId implements FormatterInterface
{
    /**
     * Generate the HTML to display the Application ID
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string HTML
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $viewHelper = $sm->get('ViewHelperManager')->get('transportManagerApplicationStatus');

        return sprintf(
            '<b>%s</b> %s',
            $data['applicationId'],
            $viewHelper->render(
                $data['transportManagerApplicationStatus']['id'],
                $data['transportManagerApplicationStatus']['description']
            )
        );
    }
}
