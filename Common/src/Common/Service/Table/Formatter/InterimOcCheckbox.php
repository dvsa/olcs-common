<?php

/**
 * Interim Operating Centres Checkbox formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Interim Operating Centres Checkbox formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InterimOcCheckbox implements FormatterInterface
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
        $format = '<input type="checkbox" value="' . $data['id'] . '" name="operatingCentres[id][]" %s>';
        if (
            isset($data['isInterim']) &&
            $data['isInterim'] == 'Y') {
            $result = sprintf($format, 'checked');
        } else {
            $result = sprintf($format, '');
        }

        return $result;
    }
}
