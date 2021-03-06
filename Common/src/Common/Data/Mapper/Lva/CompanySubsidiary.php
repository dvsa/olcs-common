<?php

/**
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;

/**
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiary implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        // @todo should be a bit more specific than this, but this is what the old code did
        return [
            'data' => $data
        ];
    }
}
