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
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['name']) && isset($data[$column['name']])) {
            $data = $data[$column['name']];
        }

        if (isset($column['addressFields'])) {

            $fields = $column['addressFields'];
        } else {
            $fields = array(
                'addressLine1',
                'addressLine2',
                'addressLine3',
                'addressLine4',
                'town',
                'postcode',
                'countryCode'
            );
        }

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

        return implode(', ', $parts);
    }
}
