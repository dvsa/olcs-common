<?php

namespace Common\Service\Api;

use Common\Util\RestClient;
use Interop\Container\ContainerInterface;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Request;
use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\Exception\InvalidServiceNameException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Uri\Http;

/**
 * Class AbstractFactory
 * @package Common\Service\Api
 */
class AbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return strpos($requestedName, 'Olcs\\RestService\\') !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $api = str_replace('Olcs\\RestService\\', '', $requestedName);

        $api = explode('\\', $api);
        if (count($api) == 1) {
            array_unshift($api, 'backend');
        }

        list($endpoint, $uri) = $api;

        $config = $container->getServiceLocator()->get('Config');
        if (!isset($config['service_api_mapping']['endpoints'][$endpoint])) {
            throw new InvalidServiceNameException('No endpoint defined for: ' . $endpoint);
        }

        /** @var \Laminas\Mvc\I18n\Translator $translator */
        $translator = $container->getServiceLocator()->get('translator');

        $filter = new CamelCaseToDash();
        $uri = strtolower($filter->filter($uri));
        $url = new Http($uri);

        $endpointConfig = $config['service_api_mapping']['endpoints'][$endpoint];
        $options = [];
        $auth = [];
        if (is_array($endpointConfig)) {
            $url =  $url->resolve($endpointConfig['url']);
            $options = $endpointConfig['options'];
            $auth = isset($endpointConfig['auth']) ? $endpointConfig['auth'] : [];
        } else {
            $url =  $url->resolve($endpointConfig);
        }

        $userRequest = $container->getServiceLocator()->get('Request');
        $secureToken = new Cookie();
        if ($userRequest instanceof Request) {
            $cookies = $userRequest->getCookie();
            if (isset($cookies['secureToken'])) {
                $secureToken = new Cookie(['secureToken' => $cookies['secureToken']]);
            }
        }

        // options
        $rest = new RestClient($url, $options, $auth, $secureToken);
        $rest->setLanguage($translator->getLocale());

        return $rest;
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator, $requestedName);
    }
}
