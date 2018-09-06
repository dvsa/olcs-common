<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;
use Common\Util\Escape;

/**
 * IRHP Permit Range table - Restricted Countries column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitRangeRestrictedCountries implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted column for the Restricted Countries
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $countryNames = [];

        foreach ($data['countrys'] as $country) {
            $countryNames[] = Escape::html($country['countryDesc']);
        }

        return implode(', ', $countryNames);
    }
}
