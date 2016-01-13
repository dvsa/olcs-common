<?php

namespace CommonTest\Service\Data;

use Common\Data\Object\Bundle;
use Common\Service\Data\BundleManagerFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class BundleManagerFactoryTest
 * @package CommonTest\Service\Data
 */
class BundleManagerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $bundle = new Bundle();
        $config = [
            'bundles' => [
                'services' => [
                    'test' => $bundle
                ]
            ]
        ];

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $mockSl->shouldIgnoreMissing();

        $sut = new BundleManagerFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Service\Data\BundleManager', $service);
        $this->assertSame($bundle, $service->get('test'));
    }
}
