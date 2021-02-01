<?php

namespace Common\FormService;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\InitializerInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class FormServiceManagerInitializer
 *
 * @package Common\FormService
 */
class FormServiceManagerInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param mixed $instance
     *
     * return mixed
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        $instance->setFormServiceLocator($container);

        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($container->getServiceLocator());
        }

        if ($instance instanceof FormHelperAwareInterface) {
            $instance->setFormHelper($container->getServiceLocator()->get('Helper\Form'));
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, $instance);
    }
}
