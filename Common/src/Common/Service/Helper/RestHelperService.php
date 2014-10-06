<?php

/**
 * Rest Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Exception\ResourceNotFoundException;
use Common\Exception\BadRequestException;
use Common\Exception\ResourceConflictException;

/**
 * Rest Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RestHelperService extends AbstractHelperService
{
    /**
     * Cache the api resolver
     *
     * @var object
     */
    protected $apiResolver;

    /**
     * Gets instance of RestClient() to make api call
     *
     * @param string $service
     */
    public function getRestClient($service)
    {
        return $this->getApiResolver()->getClient($service);
    }

    /**
     * Send a get request
     *
     * @param string $service
     * @param array $data
     * @return array
     */
    public function sendGet($service, $data = array(), $appendParamsToRoute = false)
    {
        $route = '';

        if ($appendParamsToRoute) {

            $route = '/';

            foreach ($data as $value) {
                $route .= urlencode($value) . '/';
            }

            $data = array();

            $route = rtrim($route, '/');
        }

        return $this->getRestClient($service)->get($route, $data);
    }

    /**
     * Send a post request. Bypass the checks for response when calling
     * makeRestCall
     *
     * @param string $service
     * @param array $data
     * @return array
     */
    public function sendPost($service, $data = array())
    {
        return $this->getRestClient($service)->post('', $data);
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
        $method = strtoupper($method);

        // If we are sending a GET and we don't have array data, then we have the ID we are requesting
        if ($method == 'GET' && !is_array($data)) {
            $data = array(
                'id' => $data
            );
        }

        $serviceMethod = strtolower($method);
        $path = '';

        if (!empty($bundle)) {
            $data['bundle'] = json_encode($bundle);
        }

        switch ($method) {
            case 'GET':
                if (isset($data['id'])) {
                    $handleResponseMethod = 'handleGetResponse';
                } else {
                    $handleResponseMethod = 'handleGetListResponse';
                }
                break;
            case 'POST':

                $handleResponseMethod = 'handlePostResponse';

                $data = array('data' => json_encode($data));

                break;
            // At the moment PATCH is the same as PUT
            case 'PUT':
            case 'PATCH':
                $handleResponseMethod = 'handlePutResponse';

                if (isset($data['_OPTIONS_']['multiple']) && $data['_OPTIONS_']['multiple']) {
                    $id = 0;
                } else {
                    $id = $data['id'];
                    unset($data['id']);
                }

                $path = '/' . $id;

                $data = array('data' => json_encode($data));

                break;
            case 'DELETE':
                $handleResponseMethod = 'handleDeleteResponse';
                break;
            default:
                throw new BadRequestException();
        }

        // Gets instance of RestClient to make HTTP method call to API
        $response = $this->getRestClient($service)->$serviceMethod($path, $data);

        //Handle response and return data
        return $this->handleResponseMethod($handleResponseMethod, $response);
    }

    protected function handleResponseMethod($handleResponseMethod, $response)
    {
        return $this->$handleResponseMethod($response);
    }

    protected function getApiResolver()
    {
        if ($this->apiResolver === null) {
            $this->apiResolver = $this->getServiceLocator()->get('ServiceApiResolver');
        }
        return $this->apiResolver;
    }

    /**
     * Get response will be false if 404 and array if successful
     *
     * @param mixed $response
     *
     * @return object
     */
    protected function handleGetResponse($response)
    {
        return $response;
    }

    /**
     * GetList response will be false if 404 and array if successful
     *
     * @param mixed $response
     *
     * @return object
     */
    protected function handleGetListResponse($response)
    {
        // If we have a 404
        if ($response === false) {
            throw new ResourceNotFoundException();
        }

        return $response;
    }

    /**
     * Post response
     *
     * @param mixed $response
     *
     * @return object
     */
    protected function handlePostResponse($response)
    {
        if ($response === false) {
            throw new BadRequestException();
        }

        return $response;
    }

    /**
     * Put response (This is also handling patch response at the moment)
     *
     * @param mixed $response
     *
     * @return object
     */
    protected function handlePutResponse($response)
    {
        if (is_numeric($response)) {
            switch ($response) {
                case 400:
                    throw new BadRequestException('400 Bad request');
                case 404:
                    throw new ResourceNotFoundException('Resource not found');
                case 409:
                    throw new ResourceConflictException('Version conflict');
            }
        }

        return $response;
    }

    /**
     * DELETE response will be false if 404 and array if successful
     *
     * @param mixed $response
     *
     * @return object
     */
    protected function handleDeleteResponse($response)
    {
        // If we have a 404
        if ($response === false) {
            throw new ResourceNotFoundException();
        }

        return $response;
    }
}
