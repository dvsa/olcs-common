<?php

namespace Common\Service\Table\Formatter;

/**
 * Data Retention Record Checkbox formatter
 */
class DataRetentionRecordCheckbox implements FormatterInterface
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
        $format = '<input type="checkbox" value="' . $data['id'] . '" name="id[]" %s>';

        if (isset($data['actionConfirmation']) && $data['actionConfirmation'] === true) {
            $result = sprintf($format, 'checked');
        } else {
            $result = sprintf($format, '');
        }

        return $result;
    }
}
