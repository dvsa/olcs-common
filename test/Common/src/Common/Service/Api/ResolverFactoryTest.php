<?php


namespace CommonTest\Service\Api;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Api\ResolverFactory;
use Mockery as m;

/**
 * Class ResolverFactoryTest
 * @package CommonTest\Service\Api
 */
class ResolverFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $config = [
            'rest_services' => [
                'services' => [
                    'test' => 'testService'
                ]
            ]
        ];

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new ResolverFactory();
        $instance = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Service\Api\Resolver', $instance);
        $this->assertEquals('testService', $instance->get('test'));
    }
}
