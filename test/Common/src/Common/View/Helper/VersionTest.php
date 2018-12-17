<?php

/**
 * Test Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\View\Helper;

use \Common\View\Helper\Version;

/**
 * Test Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VersionTest extends \PHPUnit\Framework\TestCase
{
    protected $viewHelper;

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
        $mockServiceLocator = $this->createMock('\Zend\ServiceManager\ServiceManager');

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $this->assertEquals($mockServiceLocator, $this->viewHelper->getServiceLocator());
    }

    /**
     * Test render without version
     */
    public function testRenderWithoutVersion()
    {
        $config = [];

        $mockServiceLocator = $this->createPartialMock(
            '\Zend\ServiceManager\ServiceManager',
            ['get', 'getServiceLocator']
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
    public function testRenderWithVersion()
    {
        $config = [
            'version' => [
                'environment' => 'Unit Test',
                'description' => 'DESCRIPTION',
                'release' => '1.0'
            ]
        ];

        $mockServiceLocator = $this->createPartialMock(
            '\Zend\ServiceManager\ServiceManager',
            ['get', 'getServiceLocator']
        );

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $helper = $this->viewHelper;

        $expected = '<div class="version-header">
    <p class="environment">Environment: <span class="environment-marker">Unit Test</span></p>
    <p class="version">Description: <span>DESCRIPTION</span></p>
    <p class="version">Version: <span>1.0</span></p>
</div>';

        $this->assertEquals($expected, $helper());
    }

    /**
     * Test render with version
     */
    public function testRenderWithoutDetails()
    {
        $config = [
            'version' => [
                'environment' => null,
                'release' => null
            ]
        ];

        $mockServiceLocator = $this->createPartialMock(
            '\Zend\ServiceManager\ServiceManager',
            ['get', 'getServiceLocator']
        );

        $this->viewHelper->setServiceLocator($mockServiceLocator);

        $mockServiceLocator->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $helper = $this->viewHelper;

        $expected = '<div class="version-header">
    <p class="environment">Environment: <span class="environment-marker">unknown</span></p>
    <p class="version">Description: <span>NA</span></p>
    <p class="version">Version: <span>unknown</span></p>
</div>';

        $this->assertEquals($expected, $helper());
    }
}
