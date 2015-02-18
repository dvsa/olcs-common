<?php

/**
 * Abstract Review Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Review Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractReviewControllerTest extends MockeryTestCase
{
    protected $adapter;
    protected $sm;
    protected $sut;

    public function setUp()
    {
        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');

        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('\Common\Controller\Lva\AbstractReviewController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setAdapter($this->adapter);
    }

    public function testIndexAction()
    {
        // Stubbed data
        $id = 123;
        $accessibleSections = ['foo'];
        $params = ['foo' => 'bar'];

        // Expectations
        $this->sut->shouldReceive('params')
            ->with('application')
            ->andReturn($id)
            ->shouldReceive('getAccessibleSections')
            ->with(true)
            ->andReturn($accessibleSections);

        $this->adapter->shouldReceive('getSectionData')
            ->with($id, $accessibleSections)
            ->andReturn($params);

        $view = $this->sut->indexAction();

        $this->assertInstanceOf('\Common\View\Model\ReviewViewModel', $view);
        $this->assertEquals($params, $view->getVariables());
    }
}
