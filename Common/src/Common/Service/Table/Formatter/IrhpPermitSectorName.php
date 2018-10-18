<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

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
            return Escape::html($data['sector']['name']);
        }

        return Escape::html($data['sector']['name']) . ": " . Escape::html($data['sector']['description']);
    }
}
