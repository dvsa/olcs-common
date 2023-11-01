<?php

namespace CommonTest\View\Factory\Helper;

use Common\Service\Helper\FlashMessengerHelperService;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Common\View\Factory\Helper\FlashMessengerFactory;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FlashMessengerFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @test
     */
    public function createService_IsCallable()
    {
        // Setup
        $sut = new FlashMessengerFactory();

        // Assert
        return $this->assertIsCallable([$sut, 'createService']);
    }

    /**
     * @test
     */
    public function createService_SetsFlashMessengerPlugin()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = new FlashMessengerFactory();

        // Execute
        $result = $sut->createService($this->setUpAbstractPluginManager($serviceLocator));

        // Assert
        $this->assertSame($serviceLocator->get('FlashMessenger'), $result->getPluginFlashMessenger());
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return array
     */
    protected function setUpDefaultServices(ServiceLocatorInterface $serviceManager): array
    {
        return [
            'FlashMessenger' => $this->setUpMockService(FlashMessenger::class),
            'Helper\FlashMessenger' => $this->setUpMockService(FlashMessengerHelperService::class),
        ];
    }
}
