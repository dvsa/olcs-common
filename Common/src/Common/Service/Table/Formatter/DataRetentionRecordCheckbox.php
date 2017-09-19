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
     * @param array $data Data of data retention row
     *
     * @return string
     */
    public static function format($data)
    {
        $format = '<input type="checkbox" value="' . $data['id'] . '" name="id[]" %s>';

        if (isset($data['actionConfirmation']) && $data['actionConfirmation'] === true) {
            $result = sprintf($format, 'checked');
        } else {
            $result = sprintf($format, '');
        }

        if ($data['nextReviewDate'] !== null) {
            $result = sprintf($format, 'disabled');
        }

        return $result;
    }
}
