<?php

/**
 * Send Transport Manager Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\SendTransportManagerApplication;

/**
 * Send Transport Manager Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendTransportManagerApplicationTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new SendTransportManagerApplication();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcess()
    {
        $params = [
            'userId' => 111,
            'applicationId' => 222,
            'dob' => '1974-05-01'
        ];

        $stubbedUser = [
            'contactDetails' => [
                'person' => [
                    'id' => 333,
                    'version' => 1
                ]
            ]
        ];

        $expectedPerson = [
            'id' => 333,
            'birthDate' => '1974-05-01',
            'version' => 1
        ];

        // Mocks
        $mockUser = m::mock();
        $mockPerson = m::mock();
        $mockTma = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTmCompleteForm = m::mock();
        $mockResponse = m::mock();

        $this->sm->setService('Entity\User', $mockUser);
        $this->sm->setService('Entity\Person', $mockPerson);
        $this->bsm->setService('Lva\TransportManagerApplicationForUser', $mockTma);
        $this->sm->setService('Email\TransportManagerCompleteDigitalForm', $mockTmCompleteForm);

        // Expectations
        $mockUser->shouldReceive('getUserDetails')
            ->once()
            ->with(111)
            ->andReturn($stubbedUser);

        $mockPerson->shouldReceive('save')
            ->once()
            ->with($expectedPerson);

        $mockTma->shouldReceive('process')
            ->once()
            ->with(['userId' => 111, 'applicationId' => 222])
            ->andReturn($mockResponse);
        $mockResponse->shouldReceive('getData')
            ->with()
            ->once()
            ->andReturn(['linkId' => 765]);

        $mockTmCompleteForm->shouldReceive('send')
            ->with(765)
            ->once();

        $this->assertEquals($mockResponse, $this->sut->process($params));
    }
}
