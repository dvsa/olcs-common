<?php

declare(strict_types=1);

namespace Common\Form\View\Helper\Extended;

use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @see FormLabel
 * @see \CommonTest\Form\View\Helper\FormLabelFactoryTest
 */
class FormLabelFactory implements FactoryInterface
{
    /**
     * @param $requestedName
     * @param array|null $options
     */
    #[\Override]
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormLabel
    {
        $instance = new FormLabel();
        $translator = $container->get(TranslatorInterface::class);
        $instance->setTranslator($translator);
        assert($translator instanceof TranslatorInterface, "Expected interface of Translator");
        return $instance;
    }
}
