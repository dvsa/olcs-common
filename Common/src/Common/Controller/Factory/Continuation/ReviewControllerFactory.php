<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\ReviewController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
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
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        return new ReviewController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper);
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
