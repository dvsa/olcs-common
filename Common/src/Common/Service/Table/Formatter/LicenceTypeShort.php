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

        $gv = LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE;
        $psv = LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE;

        if ($gvOrPsv === $gv) {
            $ref[] = 'GV';
        } elseif ($gvOrPsv === $psv) {
            $ref[] = 'PSV';
        }

        switch ($data['licence']['licenceType']['id']) {
            case LicenceEntityService::LICENCE_TYPE_RESTRICTED:
                $ref[] = 'R';
                break;
            case LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED:
                $ref[] = 'SR';
                break;
            case LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL:
                $ref[] = 'SN';
                break;
            case LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL:
                $ref[] = 'SI';
                break;
        }

        return implode('-', $ref);
    }
}
