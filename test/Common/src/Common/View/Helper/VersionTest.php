<?php

/**
 * Test Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\View\Helper;

use PHPUnit_Framework_TestCase;
use \Common\View\Helper\Version;

/**
 * Test Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->viewHelper = new Version();
    }

    /**
     * Test the service locator injection
     */
    public function testSetServiceLocator()
    {
        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager');

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $this->assertEquals($mockServiceLocator, $this->viewHelper->getServiceLocator());
    }

    /**
     * Test render without version
     */
    public function testRenderWithoutVersion()
    {
        $config = array(

        );

        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get', 'getServiceLocator'));

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $this->assertEquals('', $this->viewHelper->render());
    }

    /**
     * Test render with version
     */
    public function testRenderWithVersion()
    {
        $config = array(
            'version' => '1.0'
        );

        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get', 'getServiceLocator'));

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $this->assertEquals('V1.0', $this->viewHelper->render());
    }

    /**
     * Test invoke
     */
    public function testInvoke()
    {
        $config = array(
            'version' => '1.0'
        );

        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get', 'getServiceLocator'));

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $this->assertEquals('V1.0', $this->viewHelper->__invoke());
    }
}
