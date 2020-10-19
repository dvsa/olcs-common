<?php

/**
 * Case Link
 */
namespace Common\Service\Table\Formatter;

/**
 * Case Link
 *
 * @package Common\Service\Table\Formatter
 */
class VehicleLink implements FormatterInterface
{
    /**
     * Return a the case URL in a link format for a table.
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return sprintf(
            '<a href="%s">%s</a>',
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
