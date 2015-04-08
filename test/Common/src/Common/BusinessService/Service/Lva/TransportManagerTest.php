<?php

/**
 * Transport Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\TransportManager;
use Common\BusinessService\Response;

/**
 * Transport Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new TransportManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithCreate()
    {
        $params = [
            'data' => [
                'foo' => 'bar'
            ]
        ];

        // Mocks
        $mockTm = m::mock();
        $this->sm->setService('Entity\TransportManager', $mockTm);

        // Expectations
        $mockTm->shouldReceive('save')
            ->once()
            ->with(['foo' => 'bar'])
            ->andReturn(['id' => 111]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 111], $response->getData());
    }

    public function testProcessWithUpdate()
    {
        $params = [
            'data' => [
                'id' => 111,
                'foo' => 'bar'
            ]
        ];

        // Mocks
        $mockTm = m::mock();
        $this->sm->setService('Entity\TransportManager', $mockTm);

        // Expectations
        $mockTm->shouldReceive('save')
            ->once()
            ->with(['id' => 111, 'foo' => 'bar']);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 111], $response->getData());
    }
}
