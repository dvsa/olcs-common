<?php

/**
 * Command Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Cqrs\Command;

use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Command\CommandServiceFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Command Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandServiceFactoryTest extends MockeryTestCase
{
    /**
     * @var CommandServiceFactory
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CommandServiceFactory();
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

        $router = m::mock(\Laminas\Mvc\Router\RouteInterface::class);
        $request = m::mock(\Laminas\Http\Request::class);
        $flashMessenger = m::mock(\Common\Service\Helper\FlashMessengerService::class);

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);
        $sm->shouldReceive('get')->with('CqrsRequest')->andReturn($request);
        $sm->shouldReceive('get')->with('ApiRouter')->andReturn($router);
        $sm->shouldReceive('get')->with('Helper\FlashMessenger')->andReturn($flashMessenger)->once();

        $commandService = $this->sut->createService($sm);

        $this->assertInstanceOf(CommandService::class, $commandService);
    }
}
