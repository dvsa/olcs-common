<?php

/**
 * Transaction Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

// need to alias as RefData exists in Formatter namespace
use Common\RefData as Ref;

/**
 * Transaction Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionFeeStatus implements FormatterInterface
{
    /**
     * Format a transaction fee status
     *
     * @param array $row
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = array(), $serviceLocator = null)
    {
        $status = 'Applied';

        if (isset($row['reversingTransaction'])) {
            $router     = $serviceLocator->get('router');
            $request    = $serviceLocator->get('request');
            $urlHelper  = $serviceLocator->get('Helper\Url');
            $routeMatch = $router->match($request);
            $matchedRouteName = $routeMatch->getMatchedRouteName();

            $params = [
                'transaction' => $row['reversingTransaction']['id'],
                'action' => 'edit-fee',
            ];
            $url = $urlHelper->fromRoute($matchedRouteName, $params, [], true);

            switch ($row['reversingTransaction']['type']) {
                case Ref::TRANSACTION_TYPE_REFUND:
                    $status = 'Refunded';
                    break;
                case Ref::TRANSACTION_TYPE_REVERSAL:
                    $status = 'Reversed';
                    break;
                default:
                    $status = 'Adjusted';
                    break;
            }

            return '<a href="'. $url . '">'. $status . '</a>';
        }

        return $status;
    }
}
