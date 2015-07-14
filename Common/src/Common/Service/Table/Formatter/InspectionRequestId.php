<?php

/**
 * Inspection request ID formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Inspection request ID formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestId implements FormatterInterface
{
    /**
     * Inspection request ID as URL
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        unset($column); // parameter not used

        $urlHelper = $sm->get('Helper\Url');

        if (empty($data['application'])) {
            // licence inspection request
            $url = $urlHelper->fromRoute(
                'licence/processing/inspection-request',
                [
                    'action' => 'edit',
                    'licence' => $data['licence']['id'],
                    'id' => $data['id'],
                ]
            );
        } else {
            $route = 'lva-application/processing/inspection-request';
            $url = $urlHelper->fromRoute(
                $route,
                [
                    'action' => 'edit',
                    'application' => $data['application']['id'],
                    'id' => $data['id'],
                ]
            );
        }
        return '<a href="'
            . $url
            . '" class=js-modal-ajax>'
            . $data['id']
            . '</a>';
    }
}
