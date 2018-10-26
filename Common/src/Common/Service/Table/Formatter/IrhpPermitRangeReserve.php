<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;

/**
 * IRHP Permit Range table - Minister of State Reserve column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitRangeReserve implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted column for the State Reserve
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return $data['ssReserve'] ? 'Yes' : 'N/A';
    }
}
