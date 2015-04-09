<?php

/**
 * Transport Manager Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\TransportManagerApplicationForUser;
use Common\BusinessService\Response;
use Common\Service\Entity\TransportManagerApplicationEntityService;
use Common\Service\Entity\TransportManagerEntityService;

/**
 * Transport Manager Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerApplicationForUserTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new TransportManagerApplicationForUser();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessWithTm()
    {
        $params = [
            'userId' => 111,
            'applicationId' => 222
        ];

        $stubbedUser = [
            'transportManager' => [
                'id' => 444
            ]
        ];

        $expectedTma = [
            'tmApplicationStatus' => TransportManagerApplicationEntityService::STATUS_INCOMPLETE,
            'action' => 'A',
            'application' => 222,
            'transportManager' => 444
        ];

        // Mocks
        $mockUser = m::mock();
        $this->sm->setService('Entity\User', $mockUser);

        $mockBusinessTransportManagerApplication =
            m::mock('\Common\BusinessService\Service\Lva\TransportManagerApplication');
        $this->bsm->setService('Lva\TransportManagerApplication', $mockBusinessTransportManagerApplication);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');

        // Expectations
        $mockUser->shouldReceive('getUserDetails')
            ->once()
            ->with(111)
            ->andReturn($stubbedUser);

        $mockBusinessResponse->shouldReceive('isOk')->once()->andReturn(true);
        $mockBusinessResponse->shouldReceive('getData')->once()->andReturn(['id' => 333]);

        $mockBusinessTransportManagerApplication->shouldReceive('process')
            ->once()
            ->with(['data' => $expectedTma])
            ->andReturn($mockBusinessResponse);

        // Assertions
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['linkId' => 333], $response->getData());
    }

    public function testProcessWithoutTmWithFail()
    {
        $params = [
            'userId' => 111,
            'applicationId' => 222
        ];

        $stubbedUser = [
            'contactDetails' => [
                'id' => 555
            ],
            'transportManager' => null
        ];

        $expectedTm = [
            'data' => [
                'tmStatus' => TransportManagerEntityService::TRANSPORT_MANAGER_STATUS_CURRENT,
                'homeCd' => 555
            ]
        ];

        // Mocks
        $mockUser = m::mock();
        $mockTm = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $this->sm->setService('Entity\User', $mockUser);

        $this->bsm->setService('Lva\TransportManager', $mockTm);

        // Expectations
        $mockUser->shouldReceive('getUserDetails')
            ->once()
            ->with(111)
            ->andReturn($stubbedUser);

        $mockTm->shouldReceive('process')
            ->once()
            ->with($expectedTm)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        // Assertions
        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcessWithoutTm()
    {
        $params = [
            'userId' => 111,
            'applicationId' => 222
        ];

        $stubbedUser = [
            'id' => 666,
            'version' => 1,
            'contactDetails' => [
                'id' => 555
            ],
            'transportManager' => null
        ];

        $stubbedTm = [
            'id' => 444
        ];

        $expectedTm = [
            'data' => [
                'tmStatus' => TransportManagerEntityService::TRANSPORT_MANAGER_STATUS_CURRENT,
                'homeCd' => 555
            ]
        ];

        $expectedTma = [
            'tmApplicationStatus' => TransportManagerApplicationEntityService::STATUS_INCOMPLETE,
            'action' => 'A',
            'application' => 222,
            'transportManager' => 444
        ];

        $expectedUserData = [
            'id' => 666,
            'version' => 1,
            'transportManager' => 444
        ];

        // Mocks
        $mockUser = m::mock();
        $mockTma = m::mock();
        $mockTm = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $this->sm->setService('Entity\User', $mockUser);
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);

        $this->bsm->setService('Lva\TransportManager', $mockTm);

        $mockBusinessTransportManagerApplication =
            m::mock('\Common\BusinessService\Service\Lva\TransportManagerApplication');
        $this->bsm->setService('Lva\TransportManagerApplication', $mockBusinessTransportManagerApplication);

        $mockBusinessResponse = m::mock('\Common\BusinessService\Response');

        // Expectations
        $mockUser->shouldReceive('getUserDetails')
            ->once()
            ->with(111)
            ->andReturn($stubbedUser)
            ->shouldReceive('save')
            ->once()
            ->with($expectedUserData);

        $mockTm->shouldReceive('process')
            ->once()
            ->with($expectedTm)
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn($stubbedTm);

        $mockBusinessResponse->shouldReceive('isOk')->once()->andReturn(true);
        $mockBusinessResponse->shouldReceive('getData')->once()->andReturn(['id' => 333]);

        $mockBusinessTransportManagerApplication->shouldReceive('process')
            ->once()
            ->with(['data' => $expectedTma])
            ->andReturn($mockBusinessResponse);

        // Assertions
        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['linkId' => 333], $response->getData());
    }
}
