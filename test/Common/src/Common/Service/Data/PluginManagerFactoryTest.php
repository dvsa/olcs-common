<?php


namespace CommonTest\Service\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\PluginManagerFactory;
use Mockery as m;

/**
 * Class PluginManagerFactoryTest
 * @package CommonTest\Service\Data
 */
class PluginManagerFactoryTest extends MockeryTestCase
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
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $mockSl->shouldIgnoreMissing();

        $sut = new PluginManagerFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Service\Data\PluginManager', $service);
        $this->assertEquals('dataService', $service->get('test'));
    }
}
