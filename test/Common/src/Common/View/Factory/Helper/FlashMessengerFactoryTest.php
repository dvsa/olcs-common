<?php

namespace CommonTest\View\Factory\Helper;

use Common\Service\Helper\FlashMessengerHelperService;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Common\View\Factory\Helper\FlashMessengerFactory;
use Common\View\Helper\FlashMessenger;
use Laminas\ServiceManager\ServiceManager;

class FlashMessengerFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @test
     */
    public function invokeIsCallable(): void
    {
        // Setup
        $sut = new FlashMessengerFactory();

        // Assert
        $this->assertIsCallable([$sut, '__invoke']);
    }

    /**
     * @test
     */
    public function invokeSetsFlashMessengerPlugin(): void
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = new FlashMessengerFactory();

        // Execute
        $result = $sut->__invoke($this->setUpAbstractPluginManager($serviceLocator), FlashMessenger::class);

        // Assert
        $this->assertSame($serviceLocator->get('FlashMessenger'), $result->getPluginFlashMessenger());
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager): array
    {
        return [
            'FlashMessenger' => $this->setUpMockService(FlashMessenger::class),
            'Helper\FlashMessenger' => $this->setUpMockService(FlashMessengerHelperService::class),
        ];
    }
}
