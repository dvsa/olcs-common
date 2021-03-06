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
                'licence-type' => $data['licenceType']['id']
            ]
        ];
    }
}
