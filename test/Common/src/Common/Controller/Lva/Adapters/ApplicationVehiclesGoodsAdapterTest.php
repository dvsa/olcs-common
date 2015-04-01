<?php

/**
 * Application Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationVehiclesGoodsAdapter;

/**
 * Application Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationVehiclesGoodsAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetFormData()
    {
        $id = 3;
        $stubbedResponse = [
            'foo' => 'bar',
            'version' => 5,
            'hasEnteredReg' => 'N'
        ];
        $expectedData = [
            'data' => [
                'version' => 5,
                'hasEnteredReg' => 'N'
            ]
        ];

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getHeaderData')
            ->with($id)
            ->andReturn($stubbedResponse);

        $this->assertEquals($expectedData, $this->sut->getFormData($id));
    }

    public function testGetFormDataWithoutHasEnteredReg()
    {
        $id = 3;
        $stubbedResponse = [
            'foo' => 'bar',
            'version' => 5,
            'hasEnteredReg' => 'ABC'
        ];
        $expectedData = [
            'data' => [
                'version' => 5,
                'hasEnteredReg' => 'Y'
            ]
        ];

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getHeaderData')
            ->with($id)
            ->andReturn($stubbedResponse);

        $this->assertEquals($expectedData, $this->sut->getFormData($id));
    }
}
