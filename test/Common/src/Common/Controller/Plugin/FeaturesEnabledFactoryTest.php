<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\FeaturesEnabled;
use Common\Controller\Plugin\FeaturesEnabledFactory;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeaturesEnabledFactory
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class FeaturesEnabledFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockQuerySender = m::mock(QuerySender::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator->get')->with('QuerySender')->andReturn($mockQuerySender);
        $sut = new FeaturesEnabledFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(FeaturesEnabled::class, $service);
    }
}
