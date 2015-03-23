<?php

/**
 * Registered Address Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\RegisteredAddress;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;
use Common\Service\Entity\AddressEntityService;

/**
 * Registered Address Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RegisteredAddressTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new RegisteredAddress();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessCreateFailed()
    {
        // Params
        $address = [
            'addressLine1' => 'Foo street'
        ];
        $params = [
            'orgId' => 111,
            'registeredAddress' => $address
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockAddress = m::mock();
        $this->sm->setService('Entity\Address', $mockAddress);

        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);

        // Expectations
        $mockAddress->shouldReceive('save')
            ->with($address)
            ->andReturn(['id' => 222]);

        $mockContactDetails->shouldReceive('process')
            ->with(
                [
                    'data' => [
                        'address' => 222,
                        'contactType' => AddressEntityService::CONTACT_TYPE_REGISTERED_ADDRESS
                    ]
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        $this->assertSame($mockResponse, $this->sut->process($params));
    }

    public function testProcessCreate()
    {
        // Params
        $address = [
            'addressLine1' => 'Foo street'
        ];
        $params = [
            'orgId' => 111,
            'registeredAddress' => $address
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockAddress = m::mock();
        $this->sm->setService('Entity\Address', $mockAddress);

        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);

        // Expectations
        $mockAddress->shouldReceive('save')
            ->with($address)
            ->andReturn(['id' => 222]);

        $mockContactDetails->shouldReceive('process')
            ->with(
                [
                    'data' => [
                        'address' => 222,
                        'contactType' => AddressEntityService::CONTACT_TYPE_REGISTERED_ADDRESS
                    ]
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['id' => 333]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(
            ['hasChanged' => true, 'addressId' => 222, 'contactDetailsId' => 333],
            $response->getData()
        );
    }

    public function testProcessUpdate()
    {
        // Params
        $address = [
            'id' => 222,
            'addressLine1' => 'Foo street'
        ];
        $params = [
            'orgId' => 111,
            'registeredAddress' => $address
        ];

        // Mocks
        $mockOrganisation = m::mock();
        $mockAddress = m::mock();
        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('Entity\Address', $mockAddress);

        // Expectations
        $mockOrganisation->shouldReceive('hasChangedRegisteredAddress')
            ->with(111, $address)
            ->andReturn(true);

        $mockAddress->shouldReceive('save')
            ->with($address)
            ->andReturn([]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(
            ['hasChanged' => true, 'addressId' => 222],
            $response->getData()
        );
    }
}
