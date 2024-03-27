<?php

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicence implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        return [
            'version' => $data['version'],
            'type-of-licence' => [
                'operator-location' => $data['niFlag'],
                'operator-type' => $data['goodsOrPsv']['id'],
                'licence-type' => [
                    'licence-type' => $data['licenceType']['id'],
                    'ltyp_siContent' => [
                        'vehicle-type' => $data['vehicleType']['id'] ?? null,
                        'lgv-declaration' => [
                            'lgv-declaration-confirmation' => $data['lgvDeclarationConfirmation'] ? 1 : 0,
                        ]
                    ]
                ]
            ]
        ];
    }
}
