<?php

/**
 * Url Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use Common\Service\Helper\UrlHelperService;
use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\View\HelperPluginManager;

/**
 * Url Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UrlHelperServiceTest extends MockeryTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\UrlHelperService
     */
    private $sut;

    private $serviceManager;

    /**
     * Setup the sut
     */
    protected function setUp(): void
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new UrlHelperService();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    /**
     * @group helper_service
     * @group url_helper_service
     */
    public function testFromRoute()
    {
        $route = 'foo/bar';
        $params = array('foo' => 'bar');
        $options = array('this' => 'that');
        $reuseMatchedParams = true;
        $builtUrl = 'some/url';

        $mockUrlViewHelper = $this->attachMockUrlBuilder();
        $mockUrlViewHelper->expects($this->once())
            ->method('__invoke')
            ->with($route, $params, $options, $reuseMatchedParams)
            ->will($this->returnValue($builtUrl));

        $this->assertEquals($builtUrl, $this->sut->fromRoute($route, $params, $options, $reuseMatchedParams));
    }

    /**
     * @group helper_service
     * @group url_helper_service
     */
    public function testFromRouteWithDefaults()
    {
        $route = 'foo/bar';
        $builtUrl = 'some/url';

        $mockUrlViewHelper = $this->attachMockUrlBuilder();
        $mockUrlViewHelper->expects($this->once())
            ->method('__invoke')
            ->with($route, array(), array(), false)
            ->will($this->returnValue($builtUrl));

        $this->assertEquals($builtUrl, $this->sut->fromRoute($route));
    }

    public function testFromRouteWithHostWithNoMatchingKey()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('config')
            ->andReturn(
                [
                    'hostnames' => []
                ]
            )
            ->getMock();

        $this->sut->setServiceLocator($sm);

        try {
            $this->sut->fromRouteWithHost('foo');
        } catch (\RuntimeException $e) {
            $this->assertEquals("Hostname for 'foo' not found", $e->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testFromRouteWithHostWithAndMatchingKey()
    {
        $urlMock = function ($route, $params = null, $options) {
            $this->assertEquals('a_route', $route);
            $this->assertEquals(
                [
                    'use_canonical' => false
                ],
                $options
            );

            return '/a/url';
        };

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('config')
            ->andReturn(
                [
                    'hostnames' => [
                        'foo' => 'http://selfserve'
                    ]
                ]
            )
            ->shouldReceive('get')
            ->with('viewhelpermanager')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('url')
                ->andReturn($urlMock)
                ->getMock()
            )
            ->getMock();

        $this->sut->setServiceLocator($sm);

        $this->assertEquals(
            'http://selfserve/a/url',
            $this->sut->fromRouteWithHost('foo', 'a_route')
        );
    }

    protected function attachMockUrlBuilder()
    {
        $mockUrlViewHelper = $this->createPartialMock('\Zend\View\Helper\Url', array('__invoke'));

        $mockViewHelperManager = $this->createPartialMock(HelperPluginManager::class, array('get'));
        $mockViewHelperManager->expects($this->any())
            ->method('get')
            ->will($this->returnValue($mockUrlViewHelper));

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('viewhelpermanager', $mockViewHelperManager);

        return $mockUrlViewHelper;
    }
}
