<?php

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DashboardApplicationLink implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data['status'] !== 'apsts_not_submitted') {
            $route = 'lva-' . $column['lva'] . '/submission-summary';
        } else {
            $route = 'lva-' . $column['lva'];
        }

        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute($route, array('application' => $data['id']));

        return '<b><a href="' . $url . '">' . $data['id'] . '</a></b>';
    }
}
