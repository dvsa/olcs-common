<?php
declare(strict_types=1);

namespace CommonTest\Auth\Adapter;

use Common\Auth\Adapter\CommandAdapter;
use Common\Auth\Adapter\CommandAdapterFactory;
use Common\Auth\Listener\RefreshJWTListener;
use Common\Auth\Listener\RefreshJWTListenerFactory;
use Common\Service\Cqrs\Command\CommandSender;
use Laminas\ServiceManager\ServiceManager;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Mockery as m;

class RefreshJWTListenerFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var RefreshJWTListenerFactory
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
    public function __invoke_ReturnsAnInstanceOfRefreshJWTListener()
    {
        // Setup
        $this->setUpSut();
        $this->config(['auth' => ['session_name' => 'session']]);

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(RefreshJWTListener::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsException_WhenConfigIsMissing()
    {
        // Setup
        $this->setUpSut();
        $this->config([]);

        // Expectations
        $this->expectException(\RuntimeException::class);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    protected function setUpSut(): void
    {
        $this->sut = new RefreshJWTListenerFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->serviceManager->setService('CommandSender', m::mock(CommandSender::class));
    }

    protected function config(array $config = [])
    {
        $this->serviceManager->setService('config', $config);
    }
}
