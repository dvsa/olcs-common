<?php

/**
 * Event History Description
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Event History Description
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EventHistoryDescription implements FormatterInterface
{
    /**
     * Format
     *
     * @param array $data
     * @param array $column
     * @param null $sm
     *
     * @return string
     */
    public static function format($data, $column = [], $sm = null)
    {
        $router     = $sm->get('router');
        $request    = $sm->get('request');
        $urlHelper  = $sm->get('Helper\Url');
        $routeMatch = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        switch ($matchedRouteName) {
            case 'lva-application/processing/event-history':
            case 'lva-variation/processing/event-history':
                $entity = 'application';
                $id = $data['application']['id'];
                break;
            case 'licence/processing/event-history':
                $entity = 'licence';
                $id = $data['licence']['id'];
                break;
            case 'licence/bus-processing/event-history':
                $entity = 'busRegId';
                $id = $data['busReg'];
                break;
            case 'transport-manager/processing/event-history':
                $entity = 'transportManager';
                $id = $data['transportManager']['id'];
                break;
            case 'operator/processing/history':
                $entity = 'organisation';
                $id = $data['organisation']['id'];
                break;
            case 'processing_history':
                $entity = 'case';
                $id = $data['case']['id'];
                break;
            default;
                throw new \Exception('Not implemented');
        }
        $url = $urlHelper->fromRoute(
            $matchedRouteName,
            [
                'action' => 'edit',
                $entity => $id,
                'id' => $data['id'],
            ],
            [],
            true
        );

        if (isset($data['eventDescription'])) {
            $href = $data['eventDescription'];
        } elseif (isset($data['eventHistoryType']['description'])) {
            $href = $data['eventHistoryType']['description'];
        } else {
            $href = '';
        }

        return sprintf(
            '<a class="js-modal-ajax" href="%s">%s</a>',
            $url,
            $href
        );
    }
}
