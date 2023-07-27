<?php

declare(strict_types=1);

namespace Common\FormService\Form\Continuation;

use Common\Service\Helper\FormHelperService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ConditionsUndertakingsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ConditionsUndertakings
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConditionsUndertakings
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $formHelper = $container->get(FormHelperService::class);
        return new ConditionsUndertakings($formHelper);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ConditionsUndertakings
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ConditionsUndertakings
    {
        return $this->__invoke($serviceLocator, ConditionsUndertakings::class);
    }
}
