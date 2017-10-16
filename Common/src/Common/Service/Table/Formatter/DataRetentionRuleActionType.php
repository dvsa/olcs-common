<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;

/**
 * Data Retention Boolean formatter
 */
class DataRetentionRuleActionType implements FormatterInterface
{
    /**
     * Format
     *
     * @param array          $data   Data of current row
     * @param array          $column Column
     * @param ServiceManager $sm     Service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return $data['actionType']['id'];
    }
}
