<?php

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DashboardApplicationLink implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
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
            '<b><a href="%s">%s</a></b> <span class="%s">%s</span>',
            [
                $url,
                isset($data['licNo']) ? $data['licNo'] . '/' . $data['id'] : $data['id'],
                $statusClass,
                $data['status']['description']
            ]
        );
    }
}
