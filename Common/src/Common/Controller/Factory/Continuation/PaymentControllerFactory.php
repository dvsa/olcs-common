<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\PaymentController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\TableFactory;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class PaymentControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PaymentController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PaymentController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        $urlHelper = $container->get(UrlHelperService::class);
        $tableFactory = $container->get(TableFactory::class);
        return new PaymentController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper, $urlHelper, $tableFactory);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PaymentController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PaymentController
    {
        return $this->__invoke($serviceLocator, PaymentController::class);
    }
}
