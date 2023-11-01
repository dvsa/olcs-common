<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;

/**
 * Disqualify URL formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DisqualifyUrl implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private TreeRouteStack $router;
    private Request $request;

    /**
     * @param UrlHelperService $urlHelper
     * @param TreeRouteStack   $router
     * @param Request          $request
     */
    public function __construct(UrlHelperService $urlHelper, TreeRouteStack $router, Request $request)
    {
        $this->urlHelper = $urlHelper;
        $this->router = $router;
        $this->request = $request;
    }
    /**
     * Format a disqualify URL
     *
     * @param array $row    row
     * @param array $column column
     *
     * @return string
     */
    public function format($row, $column = [])
    {
        $routeMatch       = $this->router->match($this->request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        $query            = $this->request->getQuery()->toArray();
        $params           = $routeMatch->getParams();

        $url = '';
        switch ($matchedRouteName) {
            case 'lva-variation/people':
                $url = $this->urlHelper->fromRoute(
                    'disqualify-person/variation',
                    [
                    'variation'    => $params['application'],
                    'person'       => $row['id'],
                    ],
                    ['query' => $query],
                    true
                );
                break;
            case 'lva-licence/people':
                $url = $this->urlHelper->fromRoute(
                    'disqualify-person/licence',
                    [
                    'licence'      => $params['licence'],
                    'person'       => $row['id'],
                    ],
                    ['query' => $query],
                    true
                );
                break;
            case 'lva-application/people':
                $url = $this->urlHelper->fromRoute(
                    'disqualify-person/application',
                    [
                    'application'  => $params['application'],
                    'person'       => $row['id'],
                    ],
                    ['query' => $query],
                    true
                );
                break;
            default:
                break;
        }
        return sprintf(
            '<a href="%s" class="govuk-link js-modal-ajax">%s</a>',
            $url,
            $row['disqualificationStatus']
        );
    }
}
