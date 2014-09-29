<?php

/**
 * Make rest calls and handle the response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

/**
 * Make rest calls and handle the response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait RestCallTrait
{
    use HelperServiceAware;

    /**
     * Send a get request
     *
     * @param string $service
     * @param array $data
     * @return array
     */
    public function sendGet($service, $data = array(), $appendParamsToRoute = false)
    {
        return $this->getHelperService('RestHelper')->sendGet($service, $data, $appendParamsToRoute);
    }

    /**
     * Send a post request. Bypass the checks for response when calling makeRestCall
     *
     * @param string $service
     * @param array $data
     * @return array
     */
    public function sendPost($service, $data = array())
    {
        return $this->getHelperService('RestHelper')->sendPost($service, $data);
    }

    /**
     * Make a rest call and return the response
     *
     * @param string $service
     * @param string $method
     * @param mixed $data
     * @param array $bundle
     */
    public function makeRestCall($service, $method, $data, array $bundle = null)
    {
        return $this->getHelperService('RestHelper')->makeRestCall($service, $method, $data, $bundle);
    }

    /**
     * Gets instance of RestClient() to make api call
     *
     * @param string $service
     */
    public function getRestClient($service)
    {
        return $this->getHelperService('RestHelper')->getRestClient($service);
    }
}
