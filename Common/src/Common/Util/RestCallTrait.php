<?php

/**
 * Make rest calls and handle the response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Common\Exception\ResourceNotFoundException;
use Common\Exception\BadRequestException;
use Common\Exception\ResourceConflictException;

/**
 * Make rest calls and handle the response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait RestCallTrait
{
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
                // Currently we only handle updating 1 entity at a time
                $handleResponseMethod = 'handlePutResponse';

                $path = '/' . $data['id'];

                unset($data['id']);

                $data = array('data' => json_encode($data));

                break;
            case 'DELETE':
                $handleResponseMethod = 'handleDeleteResponse';
                break;
            // @todo implement other methods if/when necessary
            default:
                return null;
        }

        // Gets instance of RestClient to make HTTP method call to API
        $response = $this->getServiceRestClient($service, $serviceMethod, $path, $data);

        //Handle response and return data
        return $this->handleResponseMethod($handleResponseMethod, $service, $response);
    }

    public function getServiceRestClient($service, $serviceMethod, $path, $data)
    {
        return $this->getRestClient($service)->$serviceMethod($path, $data);
    }

    public function handleResponseMethod($handleResponseMethod, $service, $response)
    {
        return $this->$handleResponseMethod($service, $response);
    }

    /**
     * Gets instance of RestClient() to make api call
     *
     * @param string $service
     */
    public function getRestClient($service)
    {
        $resolveApi = $this->getServiceLocator()->get('ServiceApiResolver');
        return $resolveApi->getClient($service);
    }

    /**
     * Get response will be false if 404 and array if successful
     *
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    public function handleGetResponse($service, $response)
    {
        unset($service);

        return $response;
    }

    /**
     * GetList response will be false if 404 and array if successful
     *
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    public function handleGetListResponse($service, $response)
    {
        unset($service);

        // If we have a 404
        if ($response === false) {
            throw new ResourceNotFoundException();
        }

        return $response;
    }

    /**
     * Post response
     *
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    public function handlePostResponse($service, $response)
    {
        unset($service);

        if ($response === false) {
            throw new BadRequestException();
        }

        return $response;
    }

    /**
     * Put response (This is also handling patch response at the moment)
     *
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    public function handlePutResponse($service, $response)
    {
        unset($service);

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
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    public function handleDeleteResponse($service, $response)
    {
        unset($service);

        // If we have a 404
        if ($response === false) {
            throw new ResourceNotFoundException();
        }

        return $response;
    }
}
