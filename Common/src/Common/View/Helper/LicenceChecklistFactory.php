<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Router\Http\RouteMatch;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

class LicenceChecklistFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return LicenceChecklist
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LicenceChecklist
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $viewHelperManager->get('translate');

        return new LicenceChecklist($translator);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return LicenceChecklist
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): LicenceChecklist
    {
        return $this->__invoke($serviceLocator, null);
    }
}
