<?php

namespace Common\Service\Table\Formatter;

/**
 * Vehicle Link
 *
 * @package Common\Service\Table\Formatter
 */
class VehicleLink implements FormatterInterface
{
    /**
     * Return a vehicle URL in a link format for a table.
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return sprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            $sm->get('Helper\Url')->fromRoute(
                'licence/vehicle/view/GET',
                [
                    'vehicle' => $data['vehicle']['id']
                ],
                [],
                true
            ),
            $data['vehicle']['vrm']
        );
    }
}
