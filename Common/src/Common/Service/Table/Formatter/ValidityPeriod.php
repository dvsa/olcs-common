<?php

/**
 * Validity period formatter
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace Common\Service\Table\Formatter;

use IntlDateFormatter;
use Laminas\I18n\View\Helper\DateFormat;
use Laminas\ServiceManager\ServiceManager;

/**
 * Validity period formatter
 */
class ValidityPeriod implements FormatterInterface
{
    /**
     * @param array          $row            Row data
     * @param array          $column         Column data
     * @param ServiceManager $serviceLocator Service locator
     *
     * @return string
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $dateFormatter = $serviceLocator->get('ViewHelperManager')->get('DateFormat');
        $translator = $serviceLocator->get('Translator');
        $locale = $translator->getLocale();
        $year = $row['year'];

        return sprintf(
            $translator->translate('permits.irhp.fee-breakdown.validity-period.cell'),
            self::generateDateString($dateFormatter, $row['validFromTimestamp'], $locale, $year),
            self::generateDateString($dateFormatter, $row['validToTimestamp'], $locale, $year)
        );
    }

    /**
     * @param DateFormat $dateFormatter
     * @param int        $timestamp
     * @param string     $locale
     * @param string     $year
     *
     * @return string
     */
    private static function generateDateString($dateFormatter, $timestamp, $locale, $year)
    {
        $dateString = $dateFormatter(
            date($timestamp),
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::NONE,
            $locale
        );

        return trim(str_replace($year, '', $dateString));
    }
}
