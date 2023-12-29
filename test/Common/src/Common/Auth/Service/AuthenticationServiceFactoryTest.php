<?php
declare(strict_types=1);

namespace CommonTest\Common\Auth\Service;

use Common\Auth\Service\AuthenticationService;
use Common\Auth\Service\AuthenticationServiceFactory;
use Common\Test\MocksServicesTrait;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AuthenticationServiceFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var AuthenticationServiceFactory
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
    public function __invoke_ReturnsAnInstanceOfAuthenticationService()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(AuthenticationService::class, $result);
    }

    protected function setUpSut(): void
    {
        $this->sut = new AuthenticationServiceFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(Session::class, $this->setUpMockService(Session::class));
    }
}
