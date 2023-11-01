<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\DeclarationController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

class DeclarationControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DeclarationController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DeclarationController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        $formHelper = $container->get(FormHelperService::class);
        return new DeclarationController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper, $formHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DeclarationController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DeclarationController
    {
        return $this->__invoke($serviceLocator, DeclarationController::class);
    }
}
