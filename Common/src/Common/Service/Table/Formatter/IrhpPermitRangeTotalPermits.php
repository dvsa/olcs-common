<?php

namespace Common\Service\Table\Formatter;

/**
 * IRHP Permit Range table - Total Permits column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitRangeTotalPermits implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted column for the Total Permits
     *
     * @param array $data
     *
     * @return string
     */
    public static function format($data)
    {
        // Need to add one to get a count of all the permits inclusive
        // E.g. Permits 1 to 16 = 16 total permits.
        return ((int) $data['toNo'] - (int) $data['fromNo']) + 1;
    }
}
