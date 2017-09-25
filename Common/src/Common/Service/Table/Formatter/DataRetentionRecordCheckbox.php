<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

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
        $format = '<input type="checkbox" value="' . Escape::html($data['id']) . '" name="id[]" %s>';

        $result = sprintf($format, '');

        if ($data['nextReviewDate'] !== null) {
            $result = sprintf($format, 'disabled');
        }

        return $result;
    }
}
