<?php

namespace Common\Service\Table\Formatter;

/**
 * Disqualify URL formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DisqualifyUrl implements FormatterInterface
{
    /**
     * Format a disqualify URL
     *
     * @param array                               $row            row
     * @param array                               $column         column
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator service locator
     *
     * @return string
     */
    public static function format($row, $column = [], $serviceLocator = null)
    {
        $router           = $serviceLocator->get('router');
        $request          = $serviceLocator->get('request');
        $urlHelper        = $serviceLocator->get('Helper\Url');
        $routeMatch       = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        $query            = $request->getQuery()->toArray();
        $params           = $routeMatch->getParams();

        $url = '';
        switch ($matchedRouteName) {
            case 'lva-variation/people':
                $url = $urlHelper->fromRoute(
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
                $url = $urlHelper->fromRoute(
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
                $url = $urlHelper->fromRoute(
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
