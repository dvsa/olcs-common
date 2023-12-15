<?php

namespace CommonTest\Common\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\VehicleSearchTrait;
use Common\Service\Table\TableBuilder;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class VehicleSearchTraitStub
{
    use VehicleSearchTrait;

    public $baseRoute;
    public $lva;

    public function callAddRemovedVehiclesActions($filters, TableBuilder $table)
    {
        $this->addRemovedVehiclesActions($filters, $table);
    }
}
