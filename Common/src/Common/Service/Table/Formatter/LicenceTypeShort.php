<?php

/**
 * Licence Type Short formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\Service\Entity\LicenceEntityService;

/**
 * Licence Type Short formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTypeShort implements FormatterInterface
{
    protected static $prefixMap = [
        LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE => 'GV',
        LicenceEntityService::LICENCE_CATEGORY_PSV => 'PSV'
    ];

    protected static $suffixMap = [
        LicenceEntityService::LICENCE_TYPE_RESTRICTED => 'R',
        LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED => 'SR',
        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL => 'SN',
        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL => 'SI'
    ];

    /**
     * Retrieve a nested value
     *
     * @param array $data
     * @return string
     */
    public static function format($data)
    {
        $ref = [];

        $gvOrPsv = $data['licence']['goodsOrPsv']['id'];

        if (isset(self::$prefixMap[$gvOrPsv])) {
            $ref[] = self::$prefixMap[$gvOrPsv];
        }

        if (isset(self::$suffixMap[$data['licence']['licenceType']['id']])) {
            $ref[] = self::$suffixMap[$data['licence']['licenceType']['id']];
        }

        return implode('-', $ref);
    }
}
