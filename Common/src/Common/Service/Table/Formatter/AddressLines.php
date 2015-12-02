<?php

/**
 * Address formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Address formatted one field per line
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class AddressLines extends Address implements FormatterInterface
{
    /**
     * How to format the resulting address fields. Comma separated.
     *
     * @param $parts
     * @return string
     */
    protected static function formatAddress($parts)
    {
        return '<p>' . implode(',<br />', $parts) . '</p>';
    }
}
