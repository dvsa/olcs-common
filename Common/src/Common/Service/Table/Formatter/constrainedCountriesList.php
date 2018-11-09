<?php

/**
 * constrainedCountriesList.php
 */
namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Class ConstrainedCountriesList
 *
 * Takes a countries array and returns a comma separated list of country names.
 *
 * @package Common\Service\Table\Formatter
 */
class ConstrainedCountriesList implements FormatterInterface
{
    /**
     *
     * @param array $data The row data.
     * @param array $column The column
     * @param null $sm The service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        unset($column);

        $c = [];
        foreach ($data['constrainedCountries'] as $country) {
            $c[] = Escape::html($country['countryDesc']);
        }
        $return = empty($c) ? 'No exclusions' : implode(', ', $c);
        return $return;
    }
}
