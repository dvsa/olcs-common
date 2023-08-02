<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\FinancesController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\GuidanceHelperService;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class FinancesControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FinancesController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FinancesController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        $formHelper = $container->get(FormHelperService::class);
        $guidanceHelper = $container->get(GuidanceHelperService::class);
        return new FinancesController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper, $formHelper, $guidanceHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FinancesController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FinancesController
    {
        return $this->__invoke($serviceLocator, FinancesController::class);
    }
}
