<?php

namespace CommonTest\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\QueryService;
use Common\Service\Cqrs\Query\QueryServiceFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Client\Adapter\Curl;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Query Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryServiceFactoryTest extends MockeryTestCase
{
    /**
     * @var QueryServiceFactory
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new QueryServiceFactory();
    }

    public function testCreateService()
    {
        $adapter = m::mock(Curl::class)->makePartial();

        $config = [
            'cqrs_client' => [
                'adapter' => $adapter
            ],
            'debug' => [
                'showApiMessages' => true
            ]
        ];

        $router = m::mock(\Zend\Mvc\Router\RouteInterface::class);
        $request = m::mock(\Zend\Http\Request::class);
        $flashMessenger = m::mock(\Common\Service\Helper\FlashMessengerHelperService::class);

        /** @var ServiceManager|m\MockInterface $sm */
        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);
        $sm->shouldReceive('get')->with('CqrsRequest')->andReturn($request);
        $sm->shouldReceive('get')->with('ApiRouter')->andReturn($router);
        $sm->shouldReceive('get')->with('Helper\FlashMessenger')->andReturn($flashMessenger)->once();

        $commandService = $this->sut->createService($sm);

        $this->assertInstanceOf(QueryService::class, $commandService);
    }
}
