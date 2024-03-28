<?php

/**
 * Trailers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;

/**
 * Trailers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Trailers implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        return [
            'trailers' => [
                'shareInfo' => $data['organisation']['confirmShareTrailerInfo']
            ]
        ];
    }
}
