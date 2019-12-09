<?php

/**
 * Valid permit count formatter
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\RefData;
use Zend\ServiceManager\ServiceManager;

/**
 * Valid permit count formatter
 */
class ValidPermitCount implements FormatterInterface
{
    /**
     * Valid permit count
     *
     * @param array $row Row data
     * @param array $column Column data
     * @param ServiceManager $serviceLocator Service locator
     *
     * @return string
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $countOverrideTypeIds = [
            RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
            RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
        ];

        $permitTypeId = $row['typeId'];

        if (in_array($permitTypeId, $countOverrideTypeIds)) {
            return 1;
        }

        return $row['validPermitCount'];
    }
}
