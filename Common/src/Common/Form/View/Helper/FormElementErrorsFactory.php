<?php

declare(strict_types=1);

namespace Common\Form\View\Helper;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use HTMLPurifier;
use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see FormElementErrors
 * @see \CommonTest\Form\View\Helper\FormErrorsFactoryTest
 */
class FormElementErrorsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormElementErrors
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormElementErrors
    {
        return $this($serviceLocator, FormElementErrors::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormElementErrors
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        return new FormElementErrors(
            $container->get(FormElementMessageFormatter::class),
            $container->get(TranslatorInterface::class)
        );
    }
}
