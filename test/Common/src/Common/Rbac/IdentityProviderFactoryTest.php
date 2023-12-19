<?php
declare(strict_types=1);

namespace CommonTest\Common\Rbac;

use Common\Rbac\IdentityProviderFactory;
use Common\Rbac\JWTIdentityProvider;
use Common\Rbac\JWTIdentityProviderFactory;
use Common\Service\Cqrs\Command\CommandSender;
use Common\Test\MocksServicesTrait;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class IdentityProviderFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var JWTIdentityProviderFactory
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
        $this->sut = m::mock(JWTIdentityProviderFactory::class)->makePartial();

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
    public function __invoke_ReturnsInstance_WhenItImplementsIdentityProviderInterface()
    {
        // Setup
        $this->setUpSut();
        $this->config(['auth' => ['identity_provider' => JWTIdentityProvider::class]]);
        $this->serviceManager->setService(JWTIdentityProvider::class, $this->setUpMockService(JWTIdentityProvider::class));

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(JWTIdentityProvider::class, $result);
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
        $this->expectExceptionMessage(IdentityProviderFactory::MESSAGE_CONFIG_MISSING);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsException_WhenContainerDoesNotHaveRequestedInstance()
    {
        // Setup
        $this->setUpSut();
        $this->config(['auth' => ['identity_provider' => JWTIdentityProvider::class]]);

        // Expectations
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(IdentityProviderFactory::MESSAGE_UNABLE_TO_CREATE);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsException_WhenInstanceDoesNotImplementIdentityProviderInterface()
    {
        // Setup
        $this->setUpSut();
        $this->config(['auth' => ['identity_provider' => static::class]]);
        $this->serviceManager->setService(static::class, $this->setUpMockService(static::class));

        // Expectations
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(IdentityProviderFactory::MESSAGE_DOES_NOT_IMPLEMENT);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    protected function setUpSut(): void
    {
        $this->sut = new IdentityProviderFactory();
    }

    protected function config(array $config = [])
    {
        $this->serviceManager->setService('config', $config);
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->serviceManager->setService('CommandSender', m::mock(CommandSender::class));
    }
}
