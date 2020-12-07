<?php

/**
 * Transaction URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Transaction URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionUrl implements FormatterInterface
{
    /**
     * Format a transaction URL
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

        $url = $urlHelper->fromRoute(
            $matchedRouteName . '/transaction',
            ['transaction' => $row['transactionId']],
            ['query' => $request->getQuery()->toArray()],
            true
        );

        return '<a href="'. $url . '">'. $row['transactionId'] . '</a>';
    }
}
