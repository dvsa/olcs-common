<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
     * @param array                   $data   Row data
     * @param array                   $column Column parameters
     * @param ServiceLocatorInterface $sm     Service manager
     *
     * @return string HTML
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        $provideStatuses = [
            RefData::TMA_STATUS_INCOMPLETE,
            RefData::TMA_STATUS_AWAITING_SIGNATURE,
        ];

        if (in_array($data['transportManagerApplicationStatus']['id'], $provideStatuses, true)) {
            $linkText = 'dashboard.tm-applications.table.action.provide-details';
        } else {
            $linkText = 'dashboard.tm-applications.table.action.view-details';
        }

        return sprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            static::getApplicationUrl(
                $sm,
                $data['applicationId'],
                $data['transportManagerApplicationId'],
                $data['isVariation']
            ),
            $sm->get('translator')->translate($linkText)
        );
    }

    /**
     * Get the hyperlink for the application number
     *
     * @param ServiceLocatorInterface $sm                            Service Manager
     * @param int                     $applicationId                 Application id
     * @param int                     $transportManagerApplicationId TM Application Id
     * @param bool                    $isVariation                   Is this application a variation
     *
     * @return string URL
     */
    protected static function getApplicationUrl($sm, $applicationId, $transportManagerApplicationId, $isVariation)
    {
        $lva = ($isVariation) ? 'variation' : 'application';
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
