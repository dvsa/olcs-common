<?php

namespace CommonTest\Common\Controller\Lva\Traits;

use Common\Service\Table\TableBuilder;
use CommonTest\Common\Controller\Lva\Traits\Stubs\VehicleSearchTraitStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Controller\Lva\Traits\VehicleSearchTrait
 */
class VehicleSearchTraitTest extends MockeryTestCase
{
    /** @var  VehicleSearchTraitStub | m\MockInterface */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new VehicleSearchTraitStub();
    }

    /**
     * @dataProvider dpTestAddRemovedVehiclesActions
     */
    public function testAddRemovedVehiclesActions($filters, $actionParams)
    {
        /** @var TableBuilder | m\MockInterface $mockTbl */
        $mockTbl = m::mock(TableBuilder::class);

        $mockTbl
            ->shouldReceive('addAction')
            ->withArgs($actionParams);

        $this->sut->callAddRemovedVehiclesActions($filters, $mockTbl);
    }

    public function dpTestAddRemovedVehiclesActions()
    {
        return [
            [
                'filters' => [
                    'includeRemoved' => '1',
                ],
                'actionParams' => [
                    'hide-removed-vehicles',
                    [
                        'label' => 'label-hide-removed-vehciles',
                        'requireRows' => true,
                        'keepForReadOnly' => true,
                    ],
                ],
            ],
            [
                'filters' => [
                    'includeRemoved' => '0',
                ],
                'actionParams' => [
                    'show-removed-vehicles',
                    [
                        'label' => 'label-show-removed-vehciles',
                        'requireRows' => false,
                        'keepForReadOnly' => true,
                    ],
                ],
            ],
        ];
    }
}
