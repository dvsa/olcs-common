<?php

namespace Common\Service\Document\Bookmark\Formatter;

/**
 * Date formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Date implements FormatterInterface
{
    public static function format(array $data)
    {
        return $data['forename'] . ' ' . $data['familyName'];
    }
}
