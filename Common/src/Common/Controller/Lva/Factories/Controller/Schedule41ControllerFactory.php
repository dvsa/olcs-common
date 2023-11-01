<?php

namespace Common\Controller\Lva\Factories\Controller;

use Common\Controller\Lva\Schedule41Controller;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Table\TableFactory;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

class Schedule41ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Schedule41Controller
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Schedule41Controller
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;

        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formHelper = $container->get(FormHelperService::class);
        $tableFactory = $container->get(TableFactory::class);
        $flashMessengerHelpe = $container->get(FlashMessengerHelperService::class);

        return new Schedule41Controller(
            $niTextTranslationUtil,
            $authService,
            $formHelper,
            $tableFactory,
            $flashMessengerHelpe
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Schedule41Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Schedule41Controller
    {
        return $this->__invoke($serviceLocator, Schedule41Controller::class);
    }
}
