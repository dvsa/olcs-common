<?php

namespace Common\Service\Table\Formatter;

/**
 * IRHP Permit Sector table - Sector Name column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitSectorName implements FormatterInterface
{
    /**
     * Format
     *
     * Returns the Sector Name
     *
     * @param array $data
     *
     * @return string
     */
    public static function format($data)
    {
        if (strlen($data['sector']['description']) < 1) {
            return $data['sector']['name'];
        }

        return $data['sector']['name'] . ": " . $data['sector']['description'];
    }
}
