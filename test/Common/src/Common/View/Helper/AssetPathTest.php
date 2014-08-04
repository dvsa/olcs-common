<?php

/**
 * Test AssetPath view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\View\Helper;

use PHPUnit_Framework_TestCase;
use \Common\View\Helper\AssetPath;

/**
 * Test AssetPath view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AssetPathTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->viewHelper = $this->getMock('\Common\View\Helper\AssetPath', ['getView']);
    }

    /**
     * Test the service locator injection
     */
    public function testSetServiceLocator()
    {
        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager');

        $helper = $this->viewHelper;
        $helper->setServiceLocator($mockServiceLocator);

        $this->assertSame($mockServiceLocator, $helper->getServiceLocator());
    }

    public function testInvokeWithoutAssetPath()
    {
        $config = array();

        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get', 'getServiceLocator'));

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $mockView = $this->getMock('\stdClass', ['basePath']);
        $mockView->expects($this->once())
            ->method('basePath')
            ->with(null)
            ->will($this->returnValue('/static'));

        $this->viewHelper->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($mockView));

        $this->assertEquals('/static', $this->viewHelper->__invoke());
    }

    public function testInvokeWithAssetPath()
    {
        $config = array(
            'asset_path' => 'http://test-asset-domain'
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

        $mockView = $this->getMock('\stdClass', ['basePath']);
        $mockView->expects($this->once())
            ->method('basePath')
            ->with(null)
            ->will($this->returnValue('/static'));

        $this->viewHelper->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($mockView));

        $this->assertEquals('http://test-asset-domain/static', $this->viewHelper->__invoke());
    }

    public function testInvokeWithAssetPathAndArgument()
    {
        $config = array(
            'asset_path' => 'http://test-asset-domain'
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

        $mockView = $this->getMock('\stdClass', ['basePath']);
        $mockView->expects($this->once())
            ->method('basePath')
            ->with('/foo.png')
            ->will($this->returnValue('/static/foo.png'));

        $this->viewHelper->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($mockView));

        $this->assertEquals('http://test-asset-domain/static/foo.png', $this->viewHelper->__invoke('/foo.png'));
    }
}
