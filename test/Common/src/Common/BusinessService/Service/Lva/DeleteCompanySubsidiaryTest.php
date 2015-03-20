<?php

/**
 * Delete Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\DeleteCompanySubsidiary;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Delete Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteCompanySubsidiaryTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new DeleteCompanySubsidiary();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessWithError()
    {
        // Params
        $params = [
            'licenceId' => 999,
            'ids' => [
                111,
                222
            ]
        ];
        $stubbedCompanySubsidiary1 = [
            'name' => 'Foo'
        ];

        // Mocks
        $mockResponse = m::mock();

        $mockCompanySubsidiaryService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\CompanySubsidiaryChangeTask', $mockCompanySubsidiaryService);

        $mockCompanySubsidiaryEntity = m::mock();
        $this->sm->setService('Entity\CompanySubsidiary', $mockCompanySubsidiaryEntity);

        // Expectations
        $mockCompanySubsidiaryEntity->shouldReceive('getById')
            ->with(111)
            ->andReturn($stubbedCompanySubsidiary1)
            ->shouldReceive('delete')
            ->with(111);

        $mockResponse->shouldReceive('getType')
            ->andReturn(Response::TYPE_PERSIST_FAILED);

        $mockCompanySubsidiaryService->shouldReceive('process')
            ->with(
                [
                    'action' => 'deleted',
                    'name' => 'Foo',
                    'licenceId' => 999
                ]
            )
            ->andReturn($mockResponse);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcess()
    {
        // Params
        $params = [
            'licenceId' => 999,
            'ids' => [
                111,
                222
            ]
        ];
        $stubbedCompanySubsidiary1 = [
            'name' => 'Foo'
        ];
        $stubbedCompanySubsidiary2 = [
            'name' => 'Bar'
        ];

        // Mocks
        $mockResponse = m::mock();

        $mockCompanySubsidiaryService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\CompanySubsidiaryChangeTask', $mockCompanySubsidiaryService);

        $mockCompanySubsidiaryEntity = m::mock();
        $this->sm->setService('Entity\CompanySubsidiary', $mockCompanySubsidiaryEntity);

        // Expectations
        $mockCompanySubsidiaryEntity->shouldReceive('getById')
            ->with(111)
            ->andReturn($stubbedCompanySubsidiary1)
            ->shouldReceive('delete')
            ->with(111)
            ->shouldReceive('getById')
            ->with(222)
            ->andReturn($stubbedCompanySubsidiary2)
            ->shouldReceive('delete')
            ->with(222);

        $mockResponse->shouldReceive('getType')
            ->andReturn(Response::TYPE_PERSIST_SUCCESS);

        $mockCompanySubsidiaryService->shouldReceive('process')
            ->with(
                [
                    'action' => 'deleted',
                    'name' => 'Foo',
                    'licenceId' => 999
                ]
            )
            ->andReturn($mockResponse)
            ->shouldReceive('process')
            ->with(
                [
                    'action' => 'deleted',
                    'name' => 'Bar',
                    'licenceId' => 999
                ]
            )
            ->andReturn($mockResponse);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_PERSIST_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
        $this->assertNotSame($mockResponse, $response);
    }
}
