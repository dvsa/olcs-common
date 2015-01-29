<?php

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\Service\Entity\ApplicationEntityService;

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DashboardApplicationLink implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data['status'] !== ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED) {
            $route = 'lva-' . $column['lva'] . '/submission-summary';
        } else {
            $route = 'lva-' . $column['lva'];
        }

        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute($route, array('application' => $data['id']));

        return '<b><a href="' . $url . '">' . $data['id'] . '</a></b>';
    }
}
