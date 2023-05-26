<?php

/**
 * Transaction URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;
use Laminas\Mvc\Router\Http\TreeRouteStack;

/**
 * Transaction URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionUrl implements FormatterPluginManagerInterface
{
    private TreeRouteStack $router;
    private Request $request;
    private UrlHelperService $urlHelper;

    /**
     * @param TreeRouteStack   $router
     * @param Request          $request
     * @param UrlHelperService $urlHelper
     */
    public function __construct(TreeRouteStack $router, Request $request, UrlHelperService $urlHelper)
    {
        $this->router = $router;
        $this->request = $request;
        $this->urlHelper = $urlHelper;
    }

    /**
     * Format a transaction URL
     *
     * @param      array $row
     * @param      array $column
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = [])
    {
        $routeMatch = $this->router->match($this->request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        $url = $this->urlHelper->fromRoute(
            $matchedRouteName . '/transaction',
            ['transaction' => $row['transactionId']],
            ['query' => $this->request->getQuery()->toArray()],
            true
        );

        return '<a class="govuk-link" href="' . $url . '">' . $row['transactionId'] . '</a>';
    }
}
