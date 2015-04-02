<?php

/**
 * Licence Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceVehiclesGoodsAdapter;

/**
 * Licence Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new LicenceVehiclesGoodsAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetFormData()
    {
        $id = 3;

        $this->assertEquals([], $this->sut->getFormData($id));
    }

    public function testGetFilteredVehiclesData()
    {
        $id = 111;
        $query = [];
        $filters = [
            'page' => 1,
            'limit' => 10,
            'removalDate' => 'NULL'
        ];

        $mockAppAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockAppAdapter);

        $mockLicenceVehicle = m::mock();
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        $mockAppAdapter->shouldReceive('formatFilters')
            ->with($query)
            ->andReturn($filters);

        $mockLicenceVehicle->shouldReceive('getVehiclesDataForLicence')
            ->with(111, $filters)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getFilteredVehiclesData($id, $query));
    }
}
