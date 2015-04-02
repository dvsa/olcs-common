<?php

/**
 * Transport Manager Application Delete business service
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Transport Manager Application Delete business service
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteTransportManagerApplication implements
    BusinessServiceInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessServiceAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Delete one or multiple Transport Manager Applications
     *
     * @param array $params
     *
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $response = new Response();

        // check that ids to delete have been passed in params
        if (!isset($params['ids'])) {
            $response->setType(Response::TYPE_FAILED);
            $response->setMessage('"ids" key needs to be present in the params.');

            return $response;
        }

        $idsToDelete = $params['ids'];
        // if only one id is passed not as an array, make it into an array
        if (!is_array($idsToDelete)) {
            $idsToDelete = array($idsToDelete);
        }

        /* @var $service \Common\Service\Entity\TransportManagerApplicationEntityService */
        $service = $this->getServiceLocator()->get('Entity\TransportManagerApplication');
        $service->delete($idsToDelete);

        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
