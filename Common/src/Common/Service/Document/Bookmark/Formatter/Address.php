<?php

namespace Common\Service\Document\Bookmark\Formatter;

class Address implements FormatterInterface
{
    private $keys = [
        'addressLine1',
        'addressLine2',
        'addressLine3',
        'addressLine4',
        'town',
        'postcode'
    ];

    public static function format(array $data)
    {
        $address = [];
        foreach ($this->keys as $key) {
            if (isset($data[$key])) {
                $address[] = $data[$key];
            }
        }

        return implode("\n", $address);
    }
}
