<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\RestClientAware;
use Common\Util\RestClient;

/**
 * Class AbstractData
 * @package Olcs\Service\Data
 */
abstract class AbstractData implements RestClientAware
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
     * @param $key
     * @param $data
     * @return $this
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
}
