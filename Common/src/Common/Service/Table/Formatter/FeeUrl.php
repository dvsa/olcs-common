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
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = array(), $serviceLocator = null)
    {
        $router     = $serviceLocator->get('router');
        $request    = $serviceLocator->get('request');
        $urlHelper  = $serviceLocator->get('Helper\Url');
        $routeMatch = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        $query      = $request->getQuery()->toArray();

        // OLCS-24863 - the code below is because of the changes introduced by OLCS-23728
        // where some routing was changed to '.../table', so the following code "correct" it
        switch ($matchedRouteName) {
            case 'licence/irhp-application-fees/table':
                $matchedRouteName = 'licence/irhp-application-fees';
                break;
            case 'licence/irhp-fees/table':
                $matchedRouteName = 'licence/irhp-fees';
                break;
        }

        switch ($matchedRouteName) {
            case 'operator/fees':
            case 'licence/bus-fees':
            case 'licence/fees':
            case 'licence/irhp-fees':
            case 'licence/irhp-application-fees':
            case 'lva-application/fees':
                $url = $urlHelper->fromRoute(
                    $matchedRouteName.'/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee'],
                    ['query' => $query],
                    true
                );
                break;
            case 'fees':
                $url = $urlHelper->fromRoute('fees/pay', ['fee' => $row['id']], ['query' => $query], true);
                break;
            default:
                $url = $urlHelper->fromRoute(
                    'admin-dashboard/admin-payment-processing/misc-fees/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee', 'controller' => 'Admin\PaymentProcessingController'],
                    ['query' => $query],
                    true
                );
                break;
        }
        return '<a href="'. $url . '">'. $row['description'] . '</a>';
    }
}
