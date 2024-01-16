<?php

declare(strict_types=1);

namespace Common\Form\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @see FormElement
 * @see \CommonTest\Form\View\Helper\FormElementFactoryTest
 */
class FormElementFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param mixed $requestedName
     * @param array|null $options
     * @return FormElement
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormElement
    {
        $instance = new FormElement();
        $config = $container->get('config');
        $map = $config['form']['element']['renderers'] ?? [];
        foreach ($map as $class => $rendererClass) {
            $instance->addClass($class, $rendererClass);
        }

        return $instance;
    }
}
