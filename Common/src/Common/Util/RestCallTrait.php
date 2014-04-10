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
     * Make a rest call and return the response
     *
     * @param string $service
     * @param string $method
     * @param mixed $data
     */
    protected function makeRestCall($service, $method, $data)
    {
        $method = strtoupper($method);
        $serviceMethod = strtolower($method);
        $path = '';

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
            case 'PUT':
            // At the moment PATCH is the same as PUT
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
        
        $response = $this->service($service)->$serviceMethod($path, $data);
        
        return $this->$handleResponseMethod($service, $response);
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
        // If we have a 404
        if ($response === false) {
            // Throw a not found exception
            throw new ResourceNotFoundException();
        }

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
                    throw new BadRequestException();
                case 404:
                    throw new ResourceNotFoundException();
                case 409:
                    throw new ResourceConflictException();
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