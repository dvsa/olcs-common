<?php

/**
 * Task description formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Task description formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaskDescription implements FormatterInterface
{
    /**
     * Format a task description
     *
     * @param array $row
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = array(), $serviceLocator = null)
    {
        $router     = $serviceLocator->get('router');
        $request    = $serviceLocator->get('request');
        $routeMatch = $router->match($request);
        $params     = $routeMatch->getParams();

        // the edit URL should pass the context of the page we're on,
        // rather than the type of each task
        // (see https://jira.i-env.net/browse/OLCS-6041)
        switch ($routeMatch->getMatchedRouteName()) {
            case 'licence/processing/tasks':
                $routeParams = ['type' => 'licence', 'typeId' => $params['licence']];
                break;
            case 'lva-application/processing/tasks':
                $routeParams = ['type' => 'application', 'typeId' => $params['application']];
                break;
            case 'transport-manager/processing/tasks':
                $routeParams = ['type' => 'tm', 'typeId' => $params['transportManager']];
                break;
            case 'licence/bus-processing/tasks':
                $routeParams = [
                    'type'    => 'busreg',
                    'typeId'  => $params['busRegId'],
                    'licence' => $params['licence']
                ];
                break;
            case 'licence/irhp-application-processing/tasks':
                $routeParams = [
                    'type'    => 'irhpapplication',
                    'typeId'  => $params['irhpAppId'],
                    'licence' => $params['licence']
                ];
                break;
            case 'case_processing_tasks':
                $routeParams = ['type' => 'case', 'typeId' => $params['case']];
                break;
            case 'operator/processing/tasks':
                $routeParams = [
                    'type'    => 'organisation',
                    'typeId'  => $params['organisation']
                ];
                break;
            default:
                $routeParams = [];
                break;
        }
        $url = $serviceLocator->get('Helper\Url')->fromRoute(
            'task_action',
            array_merge(
                [
                    'task' => $row['id'],
                    'action' => 'edit',
                ],
                $routeParams
            ),
            [
                'query' => $request->getQuery()->toArray()
            ]
        );

        return sprintf('<a href="%s" class="js-modal-ajax">%s</a>', $url, $row['description']);
    }
}
