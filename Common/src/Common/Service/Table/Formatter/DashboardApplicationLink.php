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

        return vsprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            [
                $url,
                isset($data['licNo']) ? $data['licNo'] . '/' . $data['id'] : $data['id'],
            ]
        );
    }
}
