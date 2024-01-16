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
    public function __invoke_ReturnsAnInstanceOfJWTIdentityProvider()
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
        $this->sut = new JWTIdentityProviderFactory();
    }

    protected function setUpDefaultServices()
    {
        $this->serviceManager->setService('QuerySender', $this->setUpMockService(QuerySender::class));
        $this->serviceManager->setService(CacheEncryption::class, $this->setUpMockService(CacheEncryption::class));
        $this->config();
        $this->serviceManager->setService(RefreshTokenService::class, $this->setUpMockService(RefreshTokenService::class));
        $this->serviceManager->setService(Session::class, $this->setUpMockService(Session::class));
    }

    protected function config(array $config = [])
    {
        $this->serviceManager->setService('config', $config);
    }
}
