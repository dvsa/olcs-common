<?php

/**
 * Url Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\UrlHelperService;
use CommonTest\Bootstrap;

/**
 * Url Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UrlHelperServiceTest extends PHPUnit_Framework_TestCase
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
    protected function setUp()
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

    protected function attachMockUrlBuilder()
    {
        $mockUrlViewHelper = $this->getMock('\Zend\View\Helper\Url', array('__invoke'));

        $mockViewHelperManager = $this->getMock('\stdClass', array('get'));
        $mockViewHelperManager->expects($this->any())
            ->method('get')
            ->will($this->returnValue($mockUrlViewHelper));

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('viewhelpermanager', $mockViewHelperManager);

        return $mockUrlViewHelper;
    }
}
