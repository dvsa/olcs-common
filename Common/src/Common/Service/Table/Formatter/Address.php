<?php

/**
 * Address formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Address formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Address implements FormatterInterface
{
    /**
     * Format a address
     *
     * @param array $data
     * @param array $column
     * @return string
     */
    public static function format($data, $column)
    {
        $parts = array();
        foreach (array('addressLine1', 'addressLine2', 'addressLine3', 'city', 'country', 'postcode') as $item) {
            if (!empty($data[$item])) {
                $parts[] = $data[$item];
            }
        }

        return implode(', ', $parts);
    }
}
