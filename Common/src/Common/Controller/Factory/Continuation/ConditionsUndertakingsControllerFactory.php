<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\ConditionsUndertakingsController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

class ConditionsUndertakingsControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ConditionsUndertakingsController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConditionsUndertakingsController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        return new ConditionsUndertakingsController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConditionsUndertakingsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ConditionsUndertakingsController
    {
        return $this->__invoke($serviceLocator, ConditionsUndertakingsController::class);
    }
}
