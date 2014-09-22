<?php

namespace Common\Service\Document\Bookmark\Formatter;

class Name implements FormatterInterface
{
    public static function format(array $data)
    {
        return $data['forename'] . ' ' . $data['familyName'];
    }
}
