<?php
/**
 * A client for Restful API:s over HTTP
 *
 * @package     olcscommon
 * @subpackage  utility
 * @author      Pelle Wessman <pelle.wessman@valtech.se>
 */

namespace Common\Util;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Uri\Http as HttpUri;
use Zend\Http\Header\Accept;
use Common\Util\RestClient\Exception;
use Common\Util\ResponseHelper;

/**
 * A client for Restful API:s over HTTP
 */
class RestClient
{
    /**
     * @var HttpUri
     */
    public $url;

    /**
     * @var HttpClient
     */
    public $client;

    /**
     * @param HttpUri The URL of the resource that this client is meant to act on
     */
    public function __construct(HttpUri $url)
    {
        $this->url = $url;
        $this->client = new HttpClient();
    }

    /**
     * Returns the URL for a resource
     *
     * @return string
     */
    public function url($path = null)
    {
        list($path) = $this->pathOrParams($path);
        return $this->url->toString() . $path;
    }

    /**
     * Creates a resource
     *
     * Does a POST-request to the resource using the supplied parameters
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request body
     * @return mixed       Returns the body of a successful request or false if not found
     * @throws Exception   Whenever the request fails
     */
    public function create($path = null, array $params = array())
    {
        return $this->post($path, $params);
    }

    /**
     * POST:s a body to a resource
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request body
     * @return mixed       Returns the body of a successful request or false if not found
     * @throws Exception   Whenever the request fails
     */
    public function post($path = null, array $params = array())
    {
        list($path, $params) = $this->pathOrParams($path, $params);
        return $this->request('POST', $path, $params);
    }

    /**
     * Fetches data from a resource
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request
     * @return mixed       Returns the body of a successful request or false if not found
     */
    public function read($path = null, array $params = array())
    {
        return $this->get($path, $params);
    }

    /**
     * Fetches data from a resource
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request
     * @return mixed       Returns the body of a successful request or false if not found
     * @throws Exception   Whenever the request fails
     */
    public function get($path = null, array $params = array())
    {
        list($path, $params) = $this->pathOrParams($path, $params);
        return $this->request('GET', $path, $params);
    }

    /**
     * Updates a resource
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request
     * @return mixed       Returns the body of a successful request or false if not found
     */
    public function update($path = null, array $params = array())
    {
        return $this->put($path, $params);
    }

    /**
     * Replaces or creates a resource
     *
     * Does a PUT-request to the resource using the supplied parameters
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request body
     * @return mixed       Returns the body of a successful request or false if not found
     * @throws Exception   Whenever the request fails
     */
    public function put($path = null, array $params = array())
    {
        list($path, $params) = $this->pathOrParams($path, $params);
        return $this->request('PUT', $path, $params);
    }

    /**
     * Partially update a resource
     *
     * Does a PATCH-request to the resource using the supplied parameters
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request body
     * @return mixed       Returns the body of a successful request or false if not found
     * @throws Exception   Whenever the request fails
     */
    public function patch($path = null, array $params = array())
    {
        list($path, $params) = $this->pathOrParams($path, $params);
        return $this->request('PATCH', $path, $params);
    }

    /**
     * Fetches data from a resource
     *
     * @param string|array $path   The subpath of the resource or if no subpath the parameters
     * @param array        $params The parameters of the request
     * @return mixed       Returns the body of a successful request or false if not found
     */
    public function delete($path = null, array $params = array())
    {
        list($path, $params) = $this->pathOrParams($path, $params);
        return $this->request('DELETE', $path, $params);
    }

    /**
     * Makes a HTTP request
     *
     * @param  string $method HTTP method to use
     * @param  string $path   The subpath of the resource that the request is meant for
     * @param  array  $params The parameters to include in the request
     * @return mixed          Returns the body of a successful request or false if not found
     * @throws Exception      Whenever the request fails
     */
    public function request($method, $path, array $params = array())
    {
        $this->prepareRequest($method, $path, $params);

        $response = $this->client->send();

        $responseHelper = $this->getResponseHelper();

        $responseHelper->setMethod($method);
        $responseHelper->setResponse($response);
        $responseHelper->setParams($params);
        return $responseHelper->handleResponse();
    }
    
    public function getResponseHelper()
    {
        return new ResponseHelper();
    }

    /**
     * Configures the HTTP client for the request
     *
     * @see RestClient::request()
     */
    public function prepareRequest($method, $path, array $params = array())
    {
        $method = strtoupper($method);

        $accept = $this->getAccept();
        $accept->addMediaType('application/json');

        $this->client->setRequest($this->getClientRequest());

        $this->client->setUri($this->url->toString() . $path);

        $this->client->setHeaders(array(
            $accept,
        ));
        $this->client->setMethod($method);

        if ($method == 'POST' || $method == 'PUT' || $method == 'PATCH') {
            $this->client->setEncType('application/json');
            $this->client->setRawBody(json_encode($params));
        } else {
            $this->client->getRequest()->getQuery()->fromArray($params);
        }
    }
    
    public function getAccept()
    {
        return new Accept();
    }
    
    public function getClientRequest()
    {
        return new Request();
    }

    /**
     * Utility method to resolve method parameters
     *
     * @param  string|array $path   The subpath of the resource or if no subpath the parameters
     * @param  array        $params The parameters of the request body
     * @return array                First key is the path parameter, second is the params parameter
     */
    protected function pathOrParams($path, array $params = null)
    {
        $args = func_get_args();
        if (is_array($args[0])) {
            array_unshift($args, '');
        } else if (empty($args[0])) {
            $args[0] = '';
        } else if ($args[0][0] !== '/') {
            $args[0] = '/' . $path;
        }
        return $args;
    }
}
