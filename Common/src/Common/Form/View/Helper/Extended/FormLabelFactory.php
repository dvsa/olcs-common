<?php

declare(strict_types=1);

namespace Common\Form\View\Helper\Extended;

use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see FormLabel
 * @see \CommonTest\Form\View\Helper\FormLabelFactoryTest
 */
class FormLabelFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormLabel
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormLabel
    {
        return $this($serviceLocator, FormLabel::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FormLabel
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormLabel
    {
        $instance = new FormLabel();
        $translator = $container->get(TranslatorInterface::class);
        $instance->setTranslator($translator);
        assert($translator instanceof TranslatorInterface, "Expected interface of Translator");
        return $instance;
    }
}