<?php

/**
 * Address formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Address formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Address implements FormatterInterface
{
    protected static $formats = [
        'FULL' => [
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'addressLine4',
            'town',
            'postcode',
            'countryCode'
        ],
        'BRIEF' => [
            'addressLine1',
            'town',
            'postcode',
        ]
    ];

    /**
     * Format an address
     *
     * @param array                   $data   The row data.
     * @param array                   $column The column data.
     * @param ServiceLocatorInterface $sm     The service manager.
     *
     * @return string                         The formatted address
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['name'])) {
            if (strpos($column['name'], '->')) {
                $data = $sm->get('Helper\Data')->fetchNestedData($data, $column['name']);
            } elseif (isset($data[$column['name']])) {
                $data = $data[$column['name']];
            }
        }

        $fields = self::getFields($column);

        $parts = array();

        if (isset($data['countryCode']['id'])) {
            $data['countryCode'] = $data['countryCode']['id'];
        } else {
            $data['countryCode'] = null;
        }

        foreach ($fields as $item) {
            if (isset($data[$item]) && !empty($data[$item])) {
                $parts[] = $data[$item];
            }
        }

        return static::formatAddress($parts);
    }

    /**
     * How to format the resulting address fields. Comma separated.
     *
     * @param string[] $parts The address fields to format
     *
     * @return string         The formatted address fields
     */
    protected static function formatAddress($parts)
    {
        return implode(', ', $parts);
    }

    /**
     * Get the list of fields to include from the column data
     *
     * @param array $column The column data.
     *
     * @return array        The fields to include
     */
    private static function getFields($column)
    {
        if (isset($column['addressFields'])) {
            if (is_string($column['addressFields']) and array_key_exists($column['addressFields'], self::$formats)) {
                $fields = self::$formats[$column['addressFields']];
            } else {
                $fields = $column['addressFields'];
            }
        } else {
            $fields = array(
                'addressLine1',
                'town'
            );
        }
        return $fields;
    }
}
