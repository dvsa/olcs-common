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
        $pluginManager = $container;
        assert($pluginManager instanceof ServiceLocatorAwareInterface, 'Expected instance of ServiceLocatorAwareInterface');
        $serviceLocator = $pluginManager->getServiceLocator();
        return new FormElementErrors(
            $serviceLocator->get(FormElementMessageFormatter::class),
            $serviceLocator->get(TranslatorInterface::class)
        );
    }
}
