<?php

/**
 * Create/Update Transport Manager Applications
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;

/**
 * Create/Update Transport Manager Applications
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplication implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Create/Update a Transport Manager Application
     *
     * @param array $params 'data' key contains Transport Manager Application data
     * @return ResponseInterface containing the Transport Manager Application ID
     */
    public function process(array $params)
    {
        if (!isset($params['data'])) {
            throw new \InvalidArgumentException("'data' key must exists in the params.");
        }

        $saved = $this->getServiceLocator()->get('Entity\TransportManagerApplication')->save($params['data']);
        $id = (isset($saved['id'])) ? $saved['id'] : $params['data']['id'];

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        $response->setData(['id' => $id]);

        return $response;
    }
}
