<?php

/**
 * Transaction Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

// need to alias as RefData exists in Formatter namespace
use Common\RefData as Ref;
use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;

/**
 * Transaction Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionFeeStatus implements FormatterPluginManagerInterface
{
    private TreeRouteStack $router;
    private Request $request;
    private UrlHelperService $urlHelper;

    public function __construct(TreeRouteStack $router, Request $request, UrlHelperService $urlHelper)
    {
        $this->router = $router;
        $this->request = $request;
        $this->urlHelper = $urlHelper;
    }

    /**
     * Format a transaction fee status
     *
     * @param      array $row
     * @param      array $column
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = [])
    {
        $status = 'Applied';

        if (isset($row['reversingTransaction'])) {
            $routeMatch = $this->router->match($this->request);
            $matchedRouteName = $routeMatch->getMatchedRouteName();
            $params = [
                'transaction' => $row['reversingTransaction']['id'],
                'action' => 'edit-fee',
            ];
            $url = $this->urlHelper->fromRoute($matchedRouteName, $params, [], true);

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

            return '<a class="govuk-link" href="' . $url . '">' . $status . '</a>';
        }

        return $status;
    }
}
