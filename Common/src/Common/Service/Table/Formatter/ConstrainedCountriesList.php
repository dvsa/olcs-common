<?php

/**
 * ConstrainedCountriesList.php
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
        $columnName = $column['name'] ?? 'constrainedCountries';
        $translator = $sm->get('translator');

        if (empty($data[$columnName])) {
            return $translator->translate('no.constrained.countries');
        }

        $c = [];
        foreach ($data[$columnName] as $country) {
            $c[] = $translator->translate($country['countryDesc']);
        }

        return Escape::html(implode(', ', $c));
    }
}
