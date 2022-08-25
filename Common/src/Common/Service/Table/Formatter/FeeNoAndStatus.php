<?php

/**
 * Fee Number with Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Fee Number with Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeNoAndStatus implements FormatterInterface
{
    /**
     * Format a fee status
     *
     * @param array $row
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        return $row['id'] . ' ' . FeeStatus::format($row);
    }
}
