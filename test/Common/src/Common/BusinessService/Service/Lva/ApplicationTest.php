<?php

/**
 * Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\Application;
use Common\BusinessService\Response;

/**
 * Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new Application();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = [
            'id' => 111,
            'data' => ['data' => ['foo' => 'bar']]
        ];

        // Mocks
        $mockApplication = m::mock();
        $this->sm->setService('Entity\Application', $mockApplication);

        // Expectations
        $mockApplication->shouldReceive('save')
            ->with(['id' => 111, 'foo' => 'bar']);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}
