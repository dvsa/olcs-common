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
     * @param array $data   Event data
     * @param array $column Column data
     * @param null  $sm     Service Manager
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function format($data, $column = [], $sm = null)
    {
        $router     = $sm->get('router');
        $request    = $sm->get('request');
        $urlHelper  = $sm->get('Helper\Url');
        $routeMatch = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();

        $entity = self::getEntityName($data);
        // special case for busReg!
        if ($entity === 'busReg') {
            $entity = 'busRegId';
            $id = $data['busReg'];
        } else {
            $id = $data[$entity]['id'];
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

        if (isset($data['eventHistoryType']['description'])) {
            $text = $data['eventHistoryType']['description'];
        } else {
            $text = '';
        }

        return sprintf(
            '<a class="js-modal-ajax" href="%s">%s</a>',
            $url,
            $text
        );
    }

    /**
     * Discover which entity the the event is linked to
     *
     * @param array $data Event data
     *
     * @return string Entity name
     * @throws \Exception
     */
    private static function getEntityName($data)
    {
        $possibleEntities = ['application', 'licence', 'busReg', 'transportManager', 'organisation', 'case', 'irhpApplication'];

        foreach ($possibleEntities as $possibleEntity) {
            if (isset($data[$possibleEntity])) {
                return $possibleEntity;
            }
        }

        throw new \Exception('Not implemented');
    }
}
