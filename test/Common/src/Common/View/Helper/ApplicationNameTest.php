<?php

/**
 * Test ApplicationName view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\View\Helper;

use \Common\View\Helper\ApplicationName;

/**
 * Test ApplicationName view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationNameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->viewHelper = new ApplicationName();
    }

    /**
     * Test the service locator injection
     */
    public function testSetServiceLocator()
    {
        $mockServiceLocator = $this->createMock('\Zend\ServiceManager\ServiceManager');

        $helper = $this->viewHelper;

        $this->assertSame($mockServiceLocator, $helper->setServiceLocator($mockServiceLocator)->getServiceLocator());
    }

    /**
     * Test render without version
     */
    public function testRenderWithoutApplicationName()
    {
        $config = array();

        $mockServiceLocator = $this->createPartialMock(
            '\Zend\ServiceManager\ServiceManager',
            array('get', 'getServiceLocator')
        );

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
    public function testRenderWithApplicationName()
    {
        $config = array(
            'application-name' => 'Yo'
        );

        $mockServiceLocator = $this->createPartialMock(
            '\Zend\ServiceManager\ServiceManager',
            array('get', 'getServiceLocator')
        );

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $this->assertEquals('Yo', $this->viewHelper->render());
    }

    /**
     * Test invoke
     */
    public function testInvoke()
    {
        $config = array(
            'application-name' => 'Test2'
        );

        $mockServiceLocator = $this->createPartialMock(
            '\Zend\ServiceManager\ServiceManager',
            array('get', 'getServiceLocator')
        );

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $this->assertEquals('Test2', $this->viewHelper->__invoke());
    }
}
