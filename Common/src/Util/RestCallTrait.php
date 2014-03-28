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

                // Get data from entity
                $bundleHydrator = $this->getBundleHydrator();
                $data = array('data' => json_encode($bundleHydrator->getTopLevelEntitiesFromNestedEntity($data)));

                break;
            case 'PUT':
            // At the moment PATCH is the same as PUT
            case 'PATCH':
                // Currently we only handle updating 1 entity at a time
                $handleResponseMethod = 'handlePutResponse';

                // Get data from entity
                $bundleHydrator = $this->getBundleHydrator();

                $path = '/' . $data['id'];
                $data = array('data' => json_encode($data['details']));

                break;
            case 'DELETE':
                $handleResponseMethod = 'handleDeleteResponse';
                break;
            // @todo implement other methods
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

        $bundledHydrator = $this->getBundleHydrator();

        // Convert the response into entities
        $entities = $bundledHydrator->getNestedEntityFromEntities($response);

        // Get the first user entity
        return $entities[$service . '/0'];
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

        $list = array();

        $responseList = $response['Results'];

        if (!isset($response['Type']) || $response['Type'] === 'Entities') {

            $count = $response['Count'];

            $entities = $responseList;

            $bundledHydrator = $this->getBundleHydrator();

            // Convert the response into entities
            $entities = $bundledHydrator->getNestedEntityFromEntities($responseList);

            for ($i = 0; $i < $count; $i++) {
                // Get the first user entity
                $list[] = $entities[$service . '/' . $i];
            }

            return $list;
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