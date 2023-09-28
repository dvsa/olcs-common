<?php

namespace Common\Controller\Lva\Factories\Controller;

use Common\Controller\Lva\ReviewController;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class ReviewControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ReviewController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ReviewController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;

        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        return new ReviewController(
            $niTextTranslationUtil,
            $authService
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ReviewController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ReviewController
    {
        return $this->__invoke($serviceLocator, ReviewController::class);
    }
}
