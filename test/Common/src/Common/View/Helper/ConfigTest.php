<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\Config;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\View\Helper\Config
 */
class ConfigTest extends MockeryTestCase
{
    public function testInvoke()
    {
        /** @var \Zend\ServiceManager\ServiceManager | m\MockInterface $mockSl */
        $mockSl = m::mock(\Zend\ServiceManager\ServiceManager::class);
        $mockSl->shouldReceive('getServiceLocator->get')->once()->with('Config')->andReturn(['EXPECT']);

        $sut = (new Config())
            ->setServiceLocator($mockSl);

        static::assertEquals(['EXPECT'], $sut->__invoke());
    }
}
