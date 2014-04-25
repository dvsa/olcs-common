<?php
/**
 * Resolves API names to API URL:s
 * If a mapping exists in the module.config.php the base url 
 * and api path will be taken from that.
 * If no mapping exists and the api ref. is in the format [baseurl]\[path] then the
 * [baseurl] will be used to get the end point and the path will be camel cased to dashes
 * eg. LicenceVehicle to licence-vehicle.
 * If there is a path only eg. LicenceVehicle then the baseurl will default to backend
 *
 * @package     OlcsCommon
 * @subpackage  Util
 * @author      Mike Cooper
 */

namespace Common\Util;

use Zend\Uri\Http as HttpUri;
use Zend\Filter\Word\CamelCaseToDash;

/**
 * Resolves API names to API URL:s
 */
class ResolveApi
{
    /**
     * @var array
     */
    protected $mapping;

    /**
     * @param array $mapping The mapping of API:s to URL:s
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Creates and returns a client for a specific API URL.
     *
     * @param string $api The name of an API
     * @return RestClient
     * @throws Exception
     */
    public function getClient($api)
    {
        if (isset($this->mapping[$api])) {
            $url = $this->getFullApiPath($this->mapping[$api]['baseUrl'], $this->mapping[$api]['path']);
        } else {
            if (preg_match('%.+\\\.+%', $api)) {
                $service = explode('\\', $api);
                $apiPath = $service[1];
                $baseUrl = $this->getBaseUrl($service[0]);
            } else {
                $apiPath = $api;
                $baseUrl = $this->getBaseUrl('backend');
            }
            $apiPath = $this->camelCaseApiPath($apiPath);
            $url = $this->getFullApiPath($baseUrl, $apiPath);
        }
        return new RestClient($url);
    }
    
    /**
     * Gets the base url for the rest call.
     * @param type $endpoint
     * @return type
     * @throws \Exception
     */
    private function getBaseUrl($endpoint)
    {
        if (!isset($this->mapping['endpoints'][$endpoint])) {
            throw new \Exception('Invalid API enpoint');
        }
        return $this->mapping['endpoints'][$endpoint];
    }
    
    /**
     * Returns url for RestClient($url)
     * @param type $baseUrl
     * @param type $apiPath
     * @return \Zend\Uri\Http
     */
    private function getFullApiPath($baseUrl, $apiPath)
    {
        $url = new HttpUri($apiPath);
        $url->resolve($baseUrl);
        return $url;
    }
    /**
     * Returns path XxxXxx as xxx-xxx
     * @param type $apiPath
     * @return type
     */
    private function camelCaseApiPath($apiPath)
    {
        $filter = new CamelCaseToDash();
        return strtolower($filter->filter($apiPath));
    }
}
