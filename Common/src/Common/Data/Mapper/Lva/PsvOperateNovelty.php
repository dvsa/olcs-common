<?php

declare(strict_types=1);

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;

class PsvOperateNovelty implements MapperInterface
{
    public static function mapFromResult(array $data): array
    {
        return [
            'version' => $data['version'],
            'limousinesNoveltyVehicles' => [
                'psvLimousines' => $data['psvLimousines'],
                'psvNoLimousineConfirmation' => $data['psvNoLimousineConfirmation'],
                'psvOnlyLimousinesConfirmation' => $data['psvOnlyLimousinesConfirmation'],
            ]
        ];
    }

    public static function mapFromForm(array $data): array
    {
        return [
            'version' => $data['version'],
            'psvLimousines' => $data['limousinesNoveltyVehicles']['psvLimousines'],
            'psvNoLimousineConfirmation' => $data['limousinesNoveltyVehicles']['psvNoLimousineConfirmation'],
            'psvOnlyLimousinesConfirmation' => $data['limousinesNoveltyVehicles']['psvOnlyLimousinesConfirmation'],
        ];
    }
}
