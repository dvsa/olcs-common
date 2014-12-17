<?php


namespace CommonTest\Service\Data;

use Common\Data\Object\Bundle;
use Common\Service\Data\BundleManager;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class BundleManagerTest
 * @package CommonTest\Service\Data
 */
class BundleManagerTest extends TestCase
{
    public function testValidatePlugin()
    {
        $sut = new BundleManager();
        $sut->validatePlugin(new Bundle());

        $passed = false;
        try {
            $sut->validatePlugin(new \StdClass());
        } catch (\Zend\ServiceManager\Exception\RuntimeException $e) {
            if ($e->getMessage() == 'Invalid bundle class') {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception not thrown');
    }

    public function testCanCreateServiceWithName()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $sut = new BundleManager();
        $this->assertTrue($sut->canCreateServiceWithName($mockSl, 'name', 'Othername'));
    }

    public function testCreateServiceWithName()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $sut = new BundleManager();
        $service = $sut->createServiceWithName($mockSl, 'name', 'Othername');

        $this->assertInstanceOf('Common\Data\Object\Bundle', $service);
    }

    public function testInitBundle()
    {
        $mockBundle = m::mock('Common\Data\Object\Bundle');

        $sut = new BundleManager();

        $mockBundle->shouldReceive('init')->with($sut);

        $sut->initBundle($mockBundle, $sut);
    }
}
