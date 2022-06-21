<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DashboardApplicationLink implements FormatterInterface
{
    /**
     * Format column value
     *
     * @param array                   $data   Row data
     * @param array                   $column Column Parameters
     * @param ServiceLocatorInterface $sm     Service Manager
     *
     * @return string
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        if ($data['status']['id'] !== RefData::APPLICATION_STATUS_NOT_SUBMITTED) {
            $route = 'lva-' . $column['lva'] . '/submission-summary';
        } else {
            $route = 'lva-' . $column['lva'];
        }

        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute($route, array('application' => $data['id']));

        $statusClass = 'status';
        switch ($data['status']['id']) {
            case RefData::APPLICATION_STATUS_UNDER_CONSIDERATION:
                $statusClass .= ' orange';
                break;
            case RefData::APPLICATION_STATUS_VALID:
            case RefData::APPLICATION_STATUS_GRANTED:
                $statusClass .= ' green';
                break;
            case RefData::APPLICATION_STATUS_WITHDRAWN:
            case RefData::APPLICATION_STATUS_REFUSED:
            case RefData::APPLICATION_STATUS_NOT_TAKEN_UP:
                $statusClass .= ' red';
                break;
            case RefData::APPLICATION_STATUS_CANCELLED:
            case RefData::APPLICATION_STATUS_NOT_SUBMITTED:
                $statusClass .= ' grey';
                break;
            default:
                $statusClass .= ' grey';
                break;
        }

        return vsprintf(
            '<a class="overview__link" href="%s"><span class="overview__link--underline">%s</span> '.
            '<span class="overview__%s">%s</span></a>',
            [
                $url,
                isset($data['licNo']) ? $data['licNo'] . '/' . $data['id'] : $data['id'],
                $statusClass,
                $sm->get('translator')->translate($data['status']['description'])
            ]
        );
    }
}
