<?php

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\BusinessDetails;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetailsTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new BusinessDetails();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
        $this->sut->setBusinessRuleManager($this->brm);
    }

    public function testProcessMinimal()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockOrganisation = m::mock();
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessMinimalDirtyNatureOfBusiness()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockOrganisation = m::mock();
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockChangeTask = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $this->bsm->setService('Lva\BusinessDetailsChangeTask', $mockChangeTask);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(true)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $mockChangeTask->shouldReceive('process')
            ->with(['licenceId' => 222])
            ->andReturn($mockResponse);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessMinimalDirtyNatureOfBusinessWithTaskFail()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockOrganisation = m::mock();
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockChangeTask = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $this->bsm->setService('Lva\BusinessDetailsChangeTask', $mockChangeTask);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(true)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        $mockChangeTask->shouldReceive('process')
            ->with(['licenceId' => 222])
            ->andReturn($mockResponse);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcessWithTradingNamesCleanSuccess()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [
                'foo',
                'bar'
            ],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];
        $expectedTradingNamesParams = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => ['foo', 'bar']
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockOrganisation = m::mock();
        $mockTradingNames = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->bsm->setService('Lva\TradingNames', $mockTradingNames);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockTradingNames->shouldReceive('process')
            ->with($expectedTradingNamesParams)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['hasChanged' => false]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithTradingNamesCleanFailed()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [
                'foo',
                'bar'
            ],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];
        $expectedTradingNamesParams = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => ['foo', 'bar']
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockOrganisation = m::mock();
        $mockTradingNames = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->bsm->setService('Lva\TradingNames', $mockTradingNames);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockTradingNames->shouldReceive('process')
            ->with($expectedTradingNamesParams)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcessWithTradingNamesDirtySuccess()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [
                'foo',
                'bar'
            ],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];
        $expectedTradingNamesParams = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => ['foo', 'bar']
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockResponse2 = m::mock();
        $mockOrganisation = m::mock();
        $mockTradingNames = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockChangeTask = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\BusinessDetailsChangeTask', $mockChangeTask);
        $this->bsm->setService('Lva\TradingNames', $mockTradingNames);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockTradingNames->shouldReceive('process')
            ->with($expectedTradingNamesParams)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['hasChanged' => true]);

        $mockResponse2->shouldReceive('isOk')
            ->andReturn(true);

        $mockChangeTask->shouldReceive('process')
            ->with(['licenceId' => 222])
            ->andReturn($mockResponse2);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithRegisteredAddressFail()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];
        $expectedAddressParams = [
            'orgId' => 111,
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockOrganisation = m::mock();
        $mockRegisteredAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->bsm->setService('Lva\RegisteredAddress', $mockRegisteredAddress);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockRegisteredAddress->shouldReceive('process')
            ->with($expectedAddressParams)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcessWithRegisteredAddressSuccess()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];
        $expectedAddressParams = [
            'orgId' => 111,
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $responseData = [
            'hasChanged' => false,
            'contactDetailsId' => 123
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockOrganisation = m::mock();
        $mockRegisteredAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->bsm->setService('Lva\RegisteredAddress', $mockRegisteredAddress);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, 123)
            ->andReturn($validatedData);

        $mockRegisteredAddress->shouldReceive('process')
            ->with($expectedAddressParams)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn($responseData);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithRegisteredAddressWithoutContactDetails()
    {
        // Params
        $nob = ['987', '654'];
        $data = [
            'data' => [
                'natureOfBusinesses' => $nob
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [],
            'data' => $data
        ];
        $validatedData = [
            'foo' => 'bar'
        ];
        $expectedAddressParams = [
            'orgId' => 111,
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $responseData = [
            'hasChanged' => false
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockOrganisation = m::mock();
        $mockRegisteredAddress = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockBusinessRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->bsm->setService('Lva\RegisteredAddress', $mockRegisteredAddress);
        $this->brm->setService('BusinessDetails', $mockBusinessRule);
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedNatureOfBusiness')
            ->with(111, $nob)
            ->andReturn(false)
            ->shouldReceive('save')
            ->with($validatedData);

        $mockBusinessRule->shouldReceive('validate')
            ->with(111, $data, $nob, null)
            ->andReturn($validatedData);

        $mockRegisteredAddress->shouldReceive('process')
            ->with($expectedAddressParams)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn($responseData);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }
}
