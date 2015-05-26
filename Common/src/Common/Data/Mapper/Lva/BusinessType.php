<?php

/**
 * Business Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

/**
 * Business Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessType
{
    public static function mapFromResult(array $data)
    {
        return [
            'version' => $data['version'],
            'data' => [
                'type' => $data['type']['id']
            ]
        ];
    }
}
