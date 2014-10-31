<?php


namespace CommonTest\Service\Data;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Service\Data\PluginManagerFactory;
use Mockery as m;

/**
 * Class PluginManagerFactoryTest
 * @package CommonTest\Service\Data
 */
class PluginManagerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $config = [
            'data_services' => [
                'services' => [
                    'test' => 'dataService'
                ]
            ]
        ];

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->once()->with('Config')->andReturn($config);
        $mockSl->shouldIgnoreMissing();

        $sut = new PluginManagerFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Service\Data\PluginManager', $service);
        $this->assertEquals('dataService', $service->get('test'));
    }
}
