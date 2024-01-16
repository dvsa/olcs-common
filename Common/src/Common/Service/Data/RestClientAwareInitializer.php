<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\RestClientAware;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Psr\Container\ContainerInterface;

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
            /** @var \Common\Util\ResolveApi $apiResolver */
            $apiResolver = $container->get('ServiceApiResolver');
            /** @var \Laminas\Mvc\I18n\Translator $translator */
            $translator = $container->get('translator');

            $client = $apiResolver->getClient($instance->getServiceName());
            $client->setLanguage($translator->getLocale());
            $instance->setRestClient($client);
        }

        return $instance;
    }
}
