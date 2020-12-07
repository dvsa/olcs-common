<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceManager;

/**
 * IRHP Permit Range table - eplacement Stock column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitRangeReplacement implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted column for the Replacement Stock
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return $data['lostReplacement'] ? 'Yes' : 'N/A';
    }
}
