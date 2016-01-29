<?php

/**
 * Interim Vehicles Checkbox formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Interim Vehicles Checkbox formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InterimVehiclesCheckbox implements FormatterInterface
{
    /**
     * Format a checkbox
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $format = '<input type="checkbox" value="' . $data['id'] . '" name="vehicles[id][]" %s>';
        if (
            isset($data['interimApplication']) &&
            isset($data['interimApplication']['id'])
            ) {
            $result = sprintf($format, 'checked');
        } else {
            $result = sprintf($format, '');
        }

        return $result;
    }
}
