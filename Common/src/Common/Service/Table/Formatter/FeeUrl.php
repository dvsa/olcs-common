<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;

/**
 * Fee URL formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeUrl implements FormatterPluginManagerInterface
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
     * Format a fee URL
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
        $query      = $this->request->getQuery()->toArray();

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
                $url = $this->urlHelper->fromRoute(
                    $matchedRouteName . '/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee'],
                    ['query' => $query],
                    true
                );
                break;
            case 'fees':
                $url = $this->urlHelper->fromRoute('fees/pay', ['fee' => $row['id']], ['query' => $query], true);
                break;
            default:
                $url = $this->urlHelper->fromRoute(
                    'admin-dashboard/admin-payment-processing/misc-fees/fee_action',
                    ['fee' => $row['id'], 'action' => 'edit-fee', 'controller' => 'Admin\PaymentProcessingController'],
                    ['query' => $query],
                    true
                );
                break;
        }

        return '<a class="govuk-link" href="' . $url . '">' . $row['description'] . '</a>';
    }
}
