<?php

/**
 * Inspection request ID formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Inspection request ID formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestId implements FormatterInterface
{
    /**
     * Inspection request ID as URL
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $router = $sm->get('router');
        $request = $sm->get('request');
        $routeMatch = $router->match($request);
        $routeName = $routeMatch->getMatchedRouteName();
        $urlHelper = $sm->get('Helper\Url');
        switch ($routeName) {
            case 'licence/processing/inspection-request':
                $url = $urlHelper->fromRoute(
                    $routeName,
                    [
                        'action' => 'edit',
                        'licence' => $data['licence']['id'],
                        'id' => $data['id'],
                    ]
                );
                break;
            case 'lva-application/processing/inspection-request':
            case 'lva-variation/processing/inspection-request':
                $url = $urlHelper->fromRoute(
                    $routeName,
                    [
                        'action' => 'edit',
                        'application' => $data['application']['id'],
                        'id' => $data['id'],
                    ]
                );
                break;
            default:
                $url = '';
        }
        return '<a href="'
            . $url
            . '" class=js-modal-ajax>'
            . $data['id']
            . '</a>';
    }
}
