<?php

/**
 * Transaction Number formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

// need to alias as RefData exists in Formatter namespace
use Common\RefData as Ref;

/**
 * Transaction Number formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionNo implements FormatterInterface
{
    /**
     * Format a fee status
     *
     * @param array $row
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $statusClass = 'status';
        switch ($row['transaction']['status']['id']) {
            case Ref::TRANSACTION_STATUS_FAILED:
            case Ref::TRANSACTION_STATUS_CANCELLED:
                $statusClass .= ' red';
                break;
            case Ref::TRANSACTION_STATUS_PAID:
            case Ref::TRANSACTION_STATUS_COMPLETE:
                $statusClass .= ' green';
                break;
            case Ref::TRANSACTION_STATUS_OUTSTANDING:
                $statusClass .= ' orange';
                break;
            default:
                $statusClass .= ' grey';
                break;
        }

        $router     = $serviceLocator->get('router');
        $request    = $serviceLocator->get('request');
        $urlHelper  = $serviceLocator->get('Helper\Url');
        $routeMatch = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        $url = $urlHelper->fromRoute(
            $matchedRouteName.'/transaction',
            ['transaction' => $row['transaction']['id']],
            [],
            true
        );

        $link = '<a href="'. $url . '">'. $row['transaction']['id'] . '</a>';

        return vsprintf(
            '%s <span class="%s">%s</span>',
            [$link, $statusClass, $row['transaction']['status']['description']]
        );
    }
}
