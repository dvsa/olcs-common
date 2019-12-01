<?php
namespace CommonTest\Service\Data;

use Common\Data\Object\Bundle;
use Common\Service\Data\BundleManager;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Class BundleManagerTest
 * @package CommonTest\Service\Data
 */
class BundleManagerTest extends TestCase
{
    /** @var BundleManager */
    private $sut;

    public function setUp()
    {
        $this->sut = new BundleManager();
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(RuntimeException::class);

        //  call
        $this->sut->validatePlugin($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(Bundle::class);
        // make sure no exception is thrown
        $this->assertNull($this->sut->validatePlugin($plugin));
    }

    public function testCanCreateServiceWithName()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->assertTrue($this->sut->canCreateServiceWithName($mockSl, 'name', 'Othername'));
    }

    public function testCreateServiceWithName()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $service = $this->sut->createServiceWithName($mockSl, 'name', 'Othername');

        $this->assertInstanceOf('Common\Data\Object\Bundle', $service);
    }

    public function testInitBundle()
    {
        $mockBundle = m::mock('Common\Data\Object\Bundle');
        $mockBundle->shouldReceive('init')->with($this->sut);

        $this->sut->initBundle($mockBundle, $this->sut);
    }
}
