<?php

/**
 * Command Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Cqrs\Command;

use Common\Auth\Adapter\CommandAdapterFactory;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Command\CommandServiceFactory;
use Common\Service\Helper\FlashMessengerHelperService;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\Http\Request;
use Laminas\Router\RouteInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use RuntimeException;

/**
 * Command Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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
    public function createService_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'createService']);
    }

    /**
     * @test
     * @depends createService_IsCallable
     * @depends __invoke_IsCallable
     */
    public function createService_CallsInvoke()
    {
        // Setup
        $this->sut = m::mock(CommandAdapterFactory::class)->makePartial();

        // Expectations
        $this->sut->expects('__invoke')->withArgs(function ($serviceManager, $requestedName) {
            $this->assertSame($this->serviceManager(), $serviceManager, 'Expected first argument to be the ServiceManager passed to createService');
            $this->assertSame(null, $requestedName, 'Expected requestedName to be NULL');
            return true;
        });

        // Execute
        $this->sut->createService($this->serviceManager());
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
