<?php

/**
 * Make rest calls and handle the response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Util;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use OlcsEntities\Utility\BundleHydrator;
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

    private $doctrineHydrator;

    /**
     * Send a get request
     *
     * @param string $service
     * @param array $data
     * @return array
     */
    protected function sendGet($service, $data = array())
    {
        return $this->getRestClient($service)->get('', $data);
    }

    /**
     * Send a post request. Bypass the checks for response when calling
     * makeRestCall
     *
     * @param string $service
     * @param array $data
     * @return array
     */
    protected function sendPost($service, $data = array())
    {
        return $this->getRestClient($service)->post('', $data);
    }


    /**
     * Make a rest call and return the response
     *
     * @param string $service
     * @param string $method
     * @param mixed $data
     */
    protected function makeRestCall($service, $method, array $data, array $bundle = null)
    {
        $method = strtoupper($method);
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
        $response = $this->getRestClient($service)->$serviceMethod($path, $data);

        //Handle response and return data
        return $this->$handleResponseMethod($service, $response);
    }

    /**
     * Gets instance of RestClient() to make api call
     *
     * @param string $service
     */
    protected function getRestClient($service)
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
    private function handleGetResponse($service, $response)
    {
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
    private function handleGetListResponse($service, $response)
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
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    private function handlePostResponse($service, $response)
    {
        // If we have a 400
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
    private function handlePutResponse($service, $response)
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
     * @param string $service
     * @param mixed $response
     *
     * @return object
     */
    private function handleDeleteResponse($service, $response)
    {
        // If we have a 404
        if ($response === false) {
            throw new ResourceNotFoundException();
        }

        return $response;
    }

    /**
     * Return the instance of Doctrine Hydrator
     *
     * @return DoctrineHydrator
     */
    private function getDoctrineHydrator()
    {
        if (empty($this->doctrineHydrator)) {
            // Create a hydrator
            $this->doctrineHydrator = new DoctrineHydrator(
                $this->getServiceLocator()->get('doctrine.entitymanager.orm_default')
            );
        }

        return $this->doctrineHydrator;
    }

    /**
     * Return a new BundleHydrator
     *
     * @return BundleHydrator
     */
    private function getBundleHydrator()
    {
        return new BundleHydrator($this->getDoctrineHydrator());
    }
}
