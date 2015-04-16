<?php

/**
 * Dashboard Transport Manager Action Link
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Dashboard Transport Manager Action Link
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DashboardTmActionLink implements FormatterInterface
{
    /**
     * Generate the HTML to display the Action link
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string HTML
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $provideStatuses = [
            TransportManagerApplicationEntityService::STATUS_INCOMPLETE,
            TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE,
        ];

        if (in_array($data['transportManagerApplicationStatus']['id'], $provideStatuses)) {
            $linkText = 'Provide details';
        } else {
            $linkText = 'View details';
        }

        return sprintf(
            '<b><a href="%s">%s</a></b>',
            static::getApplicationUrl($sm, $data['applicationId'], $data['transportManagerApplicationId']),
            $linkText
        );

    }

    /**
     * Get the hyperlink for the application number
     *
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param int $applicationId
     * @param int $transportManagerApplicationId
     * @return string URL
     */
    protected static function getApplicationUrl($sm, $applicationId, $transportManagerApplicationId)
    {
        $lva = 'application';
        $route = 'lva-' . $lva . '/transport_manager_details';

        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(
            $route,
            ['action' => null, 'application' => $applicationId, 'child_id' => $transportManagerApplicationId],
            [],
            true
        );

        return $url;
    }
}
