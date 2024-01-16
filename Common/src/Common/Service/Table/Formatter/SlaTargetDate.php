<?php

/**
 * SlaTargetDate formatter
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;

/**
 * SlaTargetDate formatter
 * If set returns link to Sla Target date edit form, if not return link to add form with 'not set' anchor text
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTargetDate implements FormatterPluginManagerInterface
{
    private TreeRouteStack $router;
    private Request $request;
    private UrlHelperService $urlHelper;
    private Date $dateFormatter;

    public function __construct(TreeRouteStack $router, Request $request, UrlHelperService $urlHelper, Date $dateFormatter)
    {
        $this->router = $router;
        $this->request = $request;
        $this->urlHelper = $urlHelper;
        $this->dateFormatter = $dateFormatter;
    }
    /**
     * Format an SlaTargetDate
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        $routeMatch = $this->router->match($this->request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        // use agreedDate to determine if we have a record or not since mandatory field.
        // then use target date to show 'Not set' link

        if (empty($data['agreedDate'])) {
            $url = $this->urlHelper->fromRoute(
                $matchedRouteName . '/add-sla',
                [
                    'entityType' => 'document',
                    'entityId' => $data['id']
                ],
                [],
                true
            );
            return '<a href="' . $url . '" class="govuk-link js-modal-ajax">Not set</a>';
        } else {
            $url = $this->urlHelper->fromRoute(
                $matchedRouteName . '/edit-sla',
                [
                    'entityType' => 'document',
                    'entityId' => $data['id']
                ],
                [],
                true
            );

            // if target date is not set, show not set but link to the record to edit
            if (empty($data['targetDate'])) {
                return '<a href="' . $url . '" class="govuk-link js-modal-ajax">Not set</a> ';
            }

            $statusHtml = '<span class="status red">Fail</span>';

            if ($data['targetDate'] >= $data['sentDate']) {
                $statusHtml = '<span class="status green">Pass</span>';
            }
            $targetDate = $this->dateFormatter->format($data, ['name' => 'targetDate']);

            return '<a href="' . $url . '" class="govuk-link js-modal-ajax">' . $targetDate . '</a> ' . $statusHtml;
        }
    }
}
