<?php

namespace CommonTest\Common\Service\Cqrs\Command;

use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Command\CommandServiceFactory;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Test\MocksServicesTrait;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\Http\Request;
use Laminas\Router\RouteInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

class CommandServiceFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var CommandServiceFactory
     */
    protected $sut;

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    /**
     * @test
     */
    public function __invoke_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfCommandService(): void
    {
        // Setup
        $this->setUpSut();
        $this->config([
            'cqrs_client' => [
                'adapter' => m::mock(Curl::class)->makePartial()
            ],
            'debug' => [
                'showApiMessages' => true
            ],
            'auth' => [
                'session_name' => 'session'
            ]
        ]);

        // Execute
        $commandService = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(CommandService::class, $commandService);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsException_WhenConfigMissing(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $this->expectException(RuntimeException::class);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    protected function setUpSut(): void
    {
        $this->sut = new CommandServiceFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->config();
        $this->serviceManager->setService('CqrsRequest', $this->setUpMockService(Request::class));
        $this->serviceManager->setService('ApiRouter', $this->setUpMockService(RouteInterface::class));
        $this->serviceManager->setService('Helper\FlashMessenger', $this->setUpMockService(FlashMessengerHelperService::class));
    }

    protected function config(array $config = [])
    {
        $this->serviceManager->setService('Config', $config);
    }
}
