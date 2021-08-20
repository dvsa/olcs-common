<?php
declare(strict_types=1);

namespace CommonTest\Auth\Adapter;

use Common\Auth\Adapter\CommandAdapter;
use Common\Auth\Adapter\CommandAdapterFactory;
use Common\Service\Cqrs\Command\CommandSender;
use Laminas\ServiceManager\ServiceManager;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Mockery as m;

class CommandAdapterFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var CommandAdapterFactory
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
    public function __invoke_ReturnsAnInstanceOfCommandAdapter()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(CommandAdapter::class, $result);
    }

    protected function setUpSut(): void
    {
        $this->sut = new CommandAdapterFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->serviceManager->setService('CommandSender', m::mock(CommandSender::class));
    }
}
