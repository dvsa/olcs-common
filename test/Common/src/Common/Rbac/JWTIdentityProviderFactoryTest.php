<?php

declare(strict_types=1);

namespace CommonTest\Common\Rbac;

use Common\Auth\Service\RefreshTokenService;
use Common\Auth\SessionFactory;
use Common\Rbac\JWTIdentityProvider;
use Common\Rbac\JWTIdentityProviderFactory;
use Common\Service\Cqrs\Query\QuerySender;
use Common\Test\MocksServicesTrait;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Authentication\Storage\Session;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class JWTIdentityProviderFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected JWTIdentityProviderFactory $sut;

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    /**
     * @test
     */
    public function invokeIsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable(fn(\Psr\Container\ContainerInterface $container, string $requestedName, ?array $options = null): \Common\Rbac\JWTIdentityProvider => $this->sut->__invoke($container, $requestedName, $options));
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsAnInstanceOfJWTIdentityProvider(): void
    {
        // Setup
        $this->setUpSut();
        $this->config(['auth' => ['session_name' => 'session']]);

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(JWTIdentityProvider::class, $result);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeThrowsExceptionWhenConfigIsMissing(): void
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
        $this->sut = new JWTIdentityProviderFactory();
    }

    protected function setUpDefaultServices(\Laminas\ServiceManager\ServiceManager $serviceManager): void
    {
        $this->serviceManager->setService('QuerySender', $this->setUpMockService(QuerySender::class));
        $this->serviceManager->setService(CacheEncryption::class, $this->setUpMockService(CacheEncryption::class));
        $this->config();
        $this->serviceManager->setService(RefreshTokenService::class, $this->setUpMockService(RefreshTokenService::class));
        $this->serviceManager->setService(Session::class, $this->setUpMockService(Session::class));
    }

    protected function config(array $config = []): void
    {
        $this->serviceManager->setService('config', $config);
    }
}
