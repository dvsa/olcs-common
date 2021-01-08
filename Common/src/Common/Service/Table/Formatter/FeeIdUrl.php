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
class FeeIdUrl implements FormatterInterface
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

        // currently this is only used on /transaction pages
        $route = substr($matchedRouteName, 0, strpos($matchedRouteName, '/transaction'));

        $url = $urlHelper->fromRoute(
            $route,
            ['fee' => $row['id'], 'action' => 'edit-fee'],
            ['query' => $request->getQuery()->toArray()],
            true
        );

        return '<a href="'. $url . '">'. $row['id'] . '</a>';
    }
}
