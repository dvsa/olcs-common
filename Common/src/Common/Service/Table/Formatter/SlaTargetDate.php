<?php

/**
 * SlaTargetDate formatter
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * SlaTargetDate formatter
 * If set returns link to Sla Target date edit form, if not return link to add form with 'not set' anchor text
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTargetDate implements FormatterInterface
{
    /**
     * Format an SlaTargetDate
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $router     = $sm->get('router');
        $request    = $sm->get('request');
        $urlHelper  = $sm->get('Helper\Url');
        $routeMatch = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        // use agreedDate to determine if we have a record or not since mandatory field.
        // then use target date to show 'Not set' link

        if (empty($data['agreedDate'])) {
            $url = $urlHelper->fromRoute(
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
            $url = $urlHelper->fromRoute(
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
            $targetDate = Date::format($data, ['name' => 'targetDate'], $sm);

            return '<a href="' . $url . '" class="govuk-link js-modal-ajax">' . $targetDate . '</a> ' . $statusHtml;
        }
    }
}
