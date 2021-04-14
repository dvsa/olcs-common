<?php

declare(strict_types=1);

namespace Common\Form\View\Helper;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use HTMLPurifier;

/**
 * @see FormErrors
 * @see \CommonTest\Form\View\Helper\FormErrorsFactoryTest
 */
class FormErrorsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormErrors
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormErrors
    {
        return $this($serviceLocator, FormErrors::class);
    }

    /**
     * @param ContainerInterface $container
     * @param mixed $requestedName
     * @param array|null $options
     * @return FormErrors
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormErrors
    {
        $pluginManager = $container;
        assert($pluginManager instanceof ServiceLocatorAwareInterface, 'Expected instance of ServiceLocatorAwareInterface');
        $serviceLocator = $pluginManager->getServiceLocator();
        return new FormErrors(
            $serviceLocator->get(FormElementMessageFormatter::class),
            $serviceLocator->get(TranslatorInterface::class)
        );
    }
}
