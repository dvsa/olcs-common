<?php
declare(strict_types=1);

namespace CommonTest\Common\Auth\Service;

use Common\Auth\Service\RefreshTokenService;
use Common\Auth\Service\RefreshTokenServiceFactory;
use Common\Service\Cqrs\Command\CommandSender;
use Common\Test\MocksServicesTrait;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RefreshTokenServiceFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var RefreshTokenServiceFactory
     */
    protected RefreshTokenServiceFactory $sut;

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
    public function __invoke_ReturnsAnInstanceOfRefreshTokenService()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(RefreshTokenService::class, $result);
    }

    protected function setUpSut(): void
    {
        $this->sut = new RefreshTokenServiceFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService('CommandSender', $this->setUpMockService(CommandSender::class));
    }
}
