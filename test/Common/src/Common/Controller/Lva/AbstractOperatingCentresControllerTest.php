<?php

/**
 * Abstract Operating Centres Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Abstract Operating Centres Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractOperatingCentresControllerTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $adapter;

    public function setUp()
    {
        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');

        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->sut = m::mock('\Common\Controller\Lva\AbstractOperatingCentresController')->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setAdapter($this->adapter);
    }

    public function testIndexAction()
    {
        // Stubbed data
        $id = 1;
        $stubbedData = [
            'foo' => 'bar'
        ];

        // Mocked service
        $mockRequest = m::mock();
        $mockForm = m::mock();

        // Expectations
        $this->adapter->shouldReceive('addMessages')
            ->shouldReceive('getOperatingCentresFormData')
            ->with($id)
            ->andReturn($stubbedData)
            ->shouldReceive('alterFormData')
            ->with($id, $stubbedData)
            ->andReturn($stubbedData)
            ->shouldReceive('getMainForm')
            ->andReturn($mockForm)
            ->shouldReceive('attachMainScripts');

        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('application')
            ->andReturn($id);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockForm->shouldReceive('setData')
            ->with($stubbedData)
            ->andReturnSelf();

        $this->sut->shouldReceive('render')
            ->with('operating_centres', $mockForm)
            ->andReturn('VIEW');

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }
}
