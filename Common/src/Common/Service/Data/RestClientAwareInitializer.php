<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\RestClientAware;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\InitializerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class RestClientAwareInitializer
 *
 * @package Common\Service\Data
 */
class RestClientAwareInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param mixed $instance
     *
     * return mixed
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof RestClientAware) {
            $serviceLocator = $container->getServiceLocator();

            /** @var \Common\Util\ResolveApi $apiResolver */
            $apiResolver = $serviceLocator->get('ServiceApiResolver');
            /** @var \Laminas\Mvc\I18n\Translator $translator */
            $translator = $serviceLocator->get('translator');

            $client = $apiResolver->getClient($instance->getServiceName());
            $client->setLanguage($translator->getLocale());
            $instance->setRestClient($client);
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
