<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\SuccessController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

class SuccessControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SuccessController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SuccessController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        return new SuccessController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SuccessController
    {
        return $this->__invoke($serviceLocator, SuccessController::class);
    }
}
