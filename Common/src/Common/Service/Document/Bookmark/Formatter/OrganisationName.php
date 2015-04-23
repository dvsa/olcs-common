<?php

namespace Common\Service\Document\Bookmark\Formatter;

/**
 * Organisation name formatter
 */
class OrganisationName implements FormatterInterface
{
    protected static $separator = ' ';

    public static function format(array $data)
    {
        $nameParts = [];

        $nameParts[] = $data['name'];

        if (!empty($data['tradingNames'])) {
            $nameParts[] = 'T/A ' . static::getFirstTradingName($data['tradingNames']);
        }

        return implode(static::getSeparator(), $nameParts);
    }

    private static function getFirstTradingName($tradingNames)
    {
        // we could use usort here, but we don't actually want to sort
        // the whole array; we just want the earliest created so a simple
        // loop is (probably) quicker
        $first = null;
        $name = null;
        foreach ($tradingNames as $tradingName) {
            $current = strtotime($tradingName['createdOn']);
            if ($name === null || $current < $first) {
                $first = $current;
                $name = $tradingName['name'];
            }
        }

        return $name;
    }

    /**
     * @param $separator
     * @return $this
     */
    public static function setSeparator($separator)
    {
        static::$separator = $separator;
    }

    /**
     * @return string
     */
    public static function getSeparator()
    {
        return static::$separator;
    }
}
