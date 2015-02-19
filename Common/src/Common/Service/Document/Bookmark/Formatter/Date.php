<?php

namespace Common\Service\Document\Bookmark\Formatter;

/**
 * Date formatter
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class Date implements FormatterInterface
{
    public static function format(array $data)
    {
        return date("d/m/Y", strtotime(reset($data)));
    }
}
