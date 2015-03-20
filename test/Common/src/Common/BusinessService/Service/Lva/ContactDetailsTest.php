<?php

/**
 * Contact Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\ContactDetails;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Contact Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetailsTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ContactDetails();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        // Params
        $params = [
            'data' => [
                'id' => 111,
                'foo' => 'bar'
            ]
        ];
        $expectedData = [
            'id' => 111,
            'foo' => 'bar'
        ];

        // Mocks
        $mockContactDetails = m::mock();

        $this->sm->setService('Entity\ContactDetails', $mockContactDetails);

        // Expectations
        $mockContactDetails->shouldReceive('save')
            ->with($expectedData)
            ->andReturn([]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_PERSIST_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 111], $response->getData());
    }

    public function testProcessWithCreate()
    {
        // Params
        $params = [
            'data' => [
                'foo' => 'bar'
            ]
        ];
        $expectedData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockContactDetails = m::mock();

        $this->sm->setService('Entity\ContactDetails', $mockContactDetails);

        // Expectations
        $mockContactDetails->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 222]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_PERSIST_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 222], $response->getData());
    }
}
