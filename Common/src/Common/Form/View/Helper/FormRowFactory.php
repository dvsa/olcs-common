<?php

declare(strict_types=1);

namespace Common\Form\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see FormRow
 */
class FormRowFactory implements FactoryInterface
{
    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormRow
    {
        return $this($serviceLocator, FormRow::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormRow
    {
        $sm = $container->getServiceLocator();
        $mainConfig = $sm->get('Config');
        $config = $mainConfig['form_row'] ?? [];

        return new FormRow($config);
    }
}