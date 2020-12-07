<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceManager;

/**
 * Data Retention Boolean formatter
 */
class DataRetentionRuleActionType implements FormatterInterface
{
    /**
     * Format
     *
     * @param array $data Data of current row
     *
     * @return string
     */
    public static function format($data)
    {
        return htmlspecialchars($data['actionType']['id']);
    }
}
