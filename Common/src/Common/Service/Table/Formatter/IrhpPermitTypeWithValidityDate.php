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

        if (($data['typeId'] == RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID) && !empty($data['stockValidTo'])) {
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
