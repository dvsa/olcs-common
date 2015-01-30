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

    public function testGetVehiclesData()
    {
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockLicenceEntity->shouldReceive('getVehiclesDataForApplication')
            ->with(3)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getVehiclesData(3));
    }

    public function testSave()
    {
        $data = [
            'data' => [
                'foo' => 'bar'
            ]
        ];
        $id = 3;

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('save')
            ->with(['foo' => 'bar', 'id' => 3])
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->save($data, $id));
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
