<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;

/**
 * Irhp Permit Type with Validity Date formatter
 */
class IrhpPermitTypeWithValidityDate implements FormatterInterface
{
    /**
     * Format data
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $value = $data[$column['name']];

        $validityYearTypeIds = [
            RefData::ECMT_PERMIT_TYPE_ID,
            RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
        ];

        if (in_array($data['typeId'], $validityYearTypeIds) && !empty($data['stockValidTo'])) {
            $date = Date::format(
                $data,
                [
                    'name' => 'stockValidTo',
                    'dateformat' => 'Y',
                ],
                $sm
            );

            $value = sprintf('%s %s', $value, $date);
        }

        return Escape::html($value);
    }
}