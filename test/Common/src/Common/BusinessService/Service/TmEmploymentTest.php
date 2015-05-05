<?php

/**
 * TmEmployment Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\TmEmployment;
use CommonTest\Bootstrap;
use Common\Service\Entity\ContactDetailsEntityService;

/**
 * TmEmployment Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TmEmploymentTest extends MockeryTestCase
{
    /**
     * @var Common\BusinessService\Service\Lva\TmEmployment
     */
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new TmEmployment();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessAddressFailed()
    {
        $params = [
            'data' => [],
            'address' => ['foo' => 'bar']
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\Address', $mockAddress);

        // Expectations
        $mockAddress->shouldReceive('process')
            ->with(['data' => ['foo' => 'bar']])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        $this->assertSame($mockResponse, $this->sut->process($params));
    }

    public function testProcessContactDetailsFailed()
    {
        $params = [
            'data' => [],
            'address' => ['foo' => 'bar']
        ];

        $expectedContactDetails = [
            'data' => [
                'address' => 111,
                'contactType' => ContactDetailsEntityService::CONTACT_TYPE_TRANSPORT_MANAGER
            ]
        ];

        // Mocks
        $mockAddressResponse = m::mock();
        $mockCdResponse = m::mock();
        $mockAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\Address', $mockAddress);
        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);

        // Expectations
        $mockAddress->shouldReceive('process')
            ->with(['data' => ['foo' => 'bar']])
            ->andReturn($mockAddressResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($expectedContactDetails)
            ->andReturn($mockCdResponse);

        $mockAddressResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 111]);

        $mockCdResponse->shouldReceive('isOk')
            ->andReturn(false);

        $this->assertSame($mockCdResponse, $this->sut->process($params));
    }

    public function testProcessCreateEmployment()
    {
        $params = [
            'data' => [
                'bar' => 'foo'
            ],
            'address' => ['foo' => 'bar']
        ];

        $expectedContactDetails = [
            'data' => [
                'address' => 111,
                'contactType' => ContactDetailsEntityService::CONTACT_TYPE_TRANSPORT_MANAGER
            ]
        ];

        $expectedEmployment = [
            'bar' => 'foo',
            'contactDetails' => 222
        ];

        // Mocks
        $mockAddressResponse = m::mock();
        $mockCdResponse = m::mock();
        $mockTmEmployment = m::mock();
        $mockAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->sm->setService('Entity\TmEmployment', $mockTmEmployment);
        $this->bsm->setService('Lva\Address', $mockAddress);
        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);

        // Expectations
        $mockAddress->shouldReceive('process')
            ->with(['data' => ['foo' => 'bar']])
            ->andReturn($mockAddressResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($expectedContactDetails)
            ->andReturn($mockCdResponse);

        $mockAddressResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 111]);

        $mockCdResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 222]);

        $mockTmEmployment->shouldReceive('save')
            ->with($expectedEmployment)
            ->andReturn(['id' => 333]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertTrue($response->isOk());
        $this->assertEquals(['id' => 333], $response->getData());
    }

    public function testProcessUpdateEmployment()
    {
        $params = [
            'data' => [
                'id' => 333,
                'bar' => 'foo'
            ],
            'address' => ['foo' => 'bar']
        ];

        $expectedContactDetails = [
            'data' => [
                'address' => 111,
                'contactType' => ContactDetailsEntityService::CONTACT_TYPE_TRANSPORT_MANAGER
            ]
        ];

        $expectedEmployment = [
            'id' => 333,
            'bar' => 'foo',
            'contactDetails' => 222
        ];

        // Mocks
        $mockAddressResponse = m::mock();
        $mockCdResponse = m::mock();
        $mockTmEmployment = m::mock();
        $mockAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->sm->setService('Entity\TmEmployment', $mockTmEmployment);
        $this->bsm->setService('Lva\Address', $mockAddress);
        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);

        // Expectations
        $mockAddress->shouldReceive('process')
            ->with(['data' => ['foo' => 'bar']])
            ->andReturn($mockAddressResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($expectedContactDetails)
            ->andReturn($mockCdResponse);

        $mockAddressResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 111]);

        $mockCdResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 222]);

        $mockTmEmployment->shouldReceive('save')
            ->with($expectedEmployment)
            ->andReturn([]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertTrue($response->isOk());
        $this->assertEquals(['id' => 333], $response->getData());
    }

    public function testProcessUpdateEmploymentWithExistingAddress()
    {
        $params = [
            'data' => [
                'id' => 333,
                'bar' => 'foo'
            ],
            'address' => [
                'id' => 111,
                'foo' => 'bar'
            ]
        ];

        $expectedEmployment = [
            'id' => 333,
            'bar' => 'foo'
        ];

        // Mocks
        $mockAddressResponse = m::mock();
        $mockTmEmployment = m::mock();
        $mockAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->sm->setService('Entity\TmEmployment', $mockTmEmployment);
        $this->bsm->setService('Lva\Address', $mockAddress);

        // Expectations
        $mockAddress->shouldReceive('process')
            ->with(['data' => ['id' => 111, 'foo' => 'bar']])
            ->andReturn($mockAddressResponse);

        $mockAddressResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 111]);

        $mockTmEmployment->shouldReceive('save')
            ->with($expectedEmployment)
            ->andReturn([]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertTrue($response->isOk());
        $this->assertEquals(['id' => 333], $response->getData());
    }
}
