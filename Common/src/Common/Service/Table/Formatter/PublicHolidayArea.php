<?php

namespace Common\Service\Table\Formatter;

/**
 * Public holidays table - area column formatter
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class PublicHolidayArea implements FormatterPluginManagerInterface
{
    const NO_AREA = 'none';

    /**
     * Format
     *
     * @param array $data
     * @param array $column
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $map = [
            'isEngland' => 'England',
            'isWales' => 'Wales',
            'isScotland' => 'Scotland',
            'isNi' => 'Northern Ireland',
        ];

        $fncFilter = function ($key) use ($data) {
            return (isset($data[$key]) && $data[$key] === 'Y');
        };

        $result = array_keys(array_filter(array_flip($map), $fncFilter));
        //  #TODO enable it after moving to PHP 5.6
        //  $result = array_filter($map, $fncFilter, ARRAY_FILTER_USE_KEY);

        if (empty($result)) {
            return self::NO_AREA;
        }

        return implode(', ', $result);
    }
}
