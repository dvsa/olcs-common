<?php

/**
 * Inspection request ID formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;
use Laminas\Mvc\Router\Http\TreeRouteStack;

/**
 * Inspection request ID formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestId implements FormatterPluginManagerInterface
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
     * Inspection request ID as URL
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        unset($column); // parameter not used

        $routeMatch = $this->router->match($this->request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        if (substr($matchedRouteName, 0, 7) === 'licence') {
            // licence inspection request
            $url = $this->urlHelper->fromRoute(
                'licence/processing/inspection-request',
                [
                    'action' => 'edit',
                    'licence' => $data['licence']['id'],
                    'id' => $data['id'],
                ]
            );
        } else {
            $route = 'lva-application/processing/inspection-request';
            $params = $routeMatch->getParams();
            $url = $this->urlHelper->fromRoute(
                $route,
                [
                    'action' => 'edit',
                    'application' => $params['application'],
                    'id' => $data['id'],
                ]
            );
        }
        return '<a href="'
            . $url
            . '" class="govuk-link js-modal-ajax">'
            . $data['id']
            . '</a>';
    }
}
