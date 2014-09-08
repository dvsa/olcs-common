<?php

namespace Common\Service\Data;

use Common\Util\RestClient;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class AbstractData
 * @package Olcs\Service\Data
 */
abstract class AbstractData implements FactoryInterface
{
    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param \Common\Util\RestClient $restClient
     * @return $this
     */
    public function setRestClient(RestClient $restClient)
    {
        $this->restClient = $restClient;
        return $this;
    }

    /**
     * @return \Common\Util\RestClient
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param array $data
     */
    public function setData($key, $data)
    {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Common\Util\ResolveApi $apiResolver */
        $apiResolver = $serviceLocator->get('ServiceApiResolver');
        /** @var \Zend\Mvc\I18n\Translator $translator */
        $translator = $serviceLocator->get('translator');

        $client = $apiResolver->getClient($this->getServiceName());
        $client->setLanguage($translator->getLocale());
        $this->setRestClient($client);

        return $this;
    }
}
