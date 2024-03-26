<?php

declare(strict_types=1);

namespace CommonTest\Common\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\VehicleSearchTrait;
use Common\Service\Table\TableBuilder;

class VehicleSearchTraitStub
{
    use VehicleSearchTrait;

    public const SEARCH_VEHICLES_COUNT = 20;

    public $baseRoute;

    public $lva;

    public function callAddRemovedVehiclesActions($filters, TableBuilder $table): void
    {
        $this->addRemovedVehiclesActions($filters, $table);
    }
}
