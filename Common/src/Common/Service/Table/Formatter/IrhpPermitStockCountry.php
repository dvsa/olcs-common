<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Laminas\ServiceManager\ServiceManager;

/**
 * IRHP Permit Stock table - Country column formatter
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitStockCountry implements FormatterInterface
{
    /**
     * Returns the country name if applicable, along with the permit category if applicable
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $value = 'N/A';

        if (isset($data['country'])) {
            $value = $data['country']['countryDesc'];

            if (isset($data['permitCategory'])) {
                $value .= ' ' . $data['permitCategory']['description'];
            }
        }

        return Escape::html($value);
    }
}
