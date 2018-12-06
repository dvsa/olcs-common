<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\FeaturesEnabledForMethod;
use Common\Controller\Plugin\FeaturesEnabledForMethodFactory;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

class FeaturesEnabledForMethodFactoryTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testCreateService()
    {
        $mockQuerySender = m::mock(QuerySender::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator->get')->with('QuerySender')->andReturn($mockQuerySender);
        $sut = new FeaturesEnabledForMethodFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(FeaturesEnabledForMethod::class, $service);
    }
}
