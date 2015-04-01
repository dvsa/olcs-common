<?php

/**
 * Fee URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Fee URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeUrl implements FormatterInterface
{
    /**
     * Format a fee URL
     *
     * @param array $row
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator
     * @return string
     */
    public static function format($row, $column = array(), $serviceLocator = null)
    {
        $router     = $serviceLocator->get('router');
        $request    = $serviceLocator->get('request');
        $urlHelper  = $serviceLocator->get('Helper\Url');
        $routeMatch = $router->match($request);

        switch ($routeMatch->getMatchedRouteName()) {
            case 'licence/bus-fees':
                $url = $urlHelper->fromRoute(
                    'licence/bus-fees/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee', 'controller' => 'BusFeesController'],
                    [],
                    true
                );
                break;
            case 'licence/fees':
                $url = $urlHelper->fromRoute(
                    'licence/fees/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee', 'controller' => 'LicenceController'],
                    [],
                    true
                );
                break;
            case 'lva-application/fees':
                $url = $urlHelper->fromRoute(
                    'lva-application/fees/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee', 'controller' => 'ApplicationController'],
                    [],
                    true
                );
                break;
            default:
                $url = $urlHelper->fromRoute(
                    'admin-dashboard/admin-payment-processing/misc-fees/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee', 'controller' => 'Admin\PaymentProcessingController'],
                    [],
                    true
                );
                break;
        }
        return '<a href="'
            . $url
            . '" class=js-modal-ajax>'
            . $row['description']
            . '</a>';
    }
}
