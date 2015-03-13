<?php

namespace Common\Service\Document\Bookmark\Formatter;

/**
 * Time formatter
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class Time implements FormatterInterface
{
    public static function format(array $data)
    {
        return date("H:i", strtotime(reset($data)));
    }
}
