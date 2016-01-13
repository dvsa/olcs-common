<?php

/**
 * Transport Manager Review Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\TransportManagerReviewController;
use CommonTest\Bootstrap;

/**
 * Transport Manager Review Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerReviewControllerTest extends MockeryTestCase
{
    /**
     * @var Common\Controller\TransportManagerReviewController
     */
    protected $sut;

    protected $sm;

    protected $pm;

    public function setUp()
    {
        $this->markTestSkipped();
        $this->sut = new TransportManagerReviewController();
        $this->sm = Bootstrap::getServiceManager();
        $this->pm = m::mock('\Zend\Mvc\Controller\PluginManager')->makePartial();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setPluginManager($this->pm);
    }

    public function testIndexAction()
    {
        $config = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockTm = m::mock();
        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');
        $this->sm->setService('Helper\TransportManager', $mockTm);
        $this->pm->setService('params', $mockParams);

        // Expectations
        $mockTm->shouldReceive('getReviewConfig')
            ->with(111)
            ->andReturn($config);

        $mockParams
            ->shouldReceive('setController')
            ->with($this->sut)
            ->shouldReceive('__invoke')
            ->with('id')
            ->andReturn(111);

        $response = $this->sut->indexAction();

        $this->assertInstanceOf('\Common\View\Model\ReviewViewModel', $response);
        $this->assertEquals($config, $response->getVariables());
    }
}
