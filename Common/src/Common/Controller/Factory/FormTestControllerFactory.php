<?php

namespace Common\Controller\Factory;

use Common\Controller\FormTestController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FormTestControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormTestController
    {
        $formServiceManager = $container->get(FormServiceManager::class);
        $formHelperService = $container->get(FormHelperService::class);
        return new FormTestController($formServiceManager, $formHelperService);
    }
}
