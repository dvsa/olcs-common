<?php

namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Return To Address
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReturnToAddress extends AbstractHelper
{
    protected static $addresses = [
        'ni' => [
            'Department for Infrastructure',
            'The Central Licensing Office',
            'PO Box 180',
            'Leeds',
            'LS9 1BU'
        ],
        'gb' => [
            'Office of the Traffic Commissioner',
            'The Central Licensing Office',
            'Hillcrest House',
            '386 Harehills Lane',
            'Leeds',
            'LS9 6NF'
        ]
    ];

    /**
     * Print out address
     *
     * @param bool   $isNi      Is NI
     * @param string $separator Default line separator
     *
     * @return string
     */
    public function __invoke($isNi = false, $separator = ', ')
    {
        return self::getAddress($isNi, $separator);
    }

    /**
     * Print out address
     *
     * @param bool   $isNi      Is NI
     * @param string $separator Default line separator
     *
     * @return string
     */
    public static function getAddress($isNi = false, $separator = ', ')
    {
        if ($isNi) {
            return implode($separator, self::$addresses['ni']);
        }

        return implode($separator, self::$addresses['gb']);
    }
}
