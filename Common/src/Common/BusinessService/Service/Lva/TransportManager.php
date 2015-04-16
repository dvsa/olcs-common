<?php

/**
 * Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;

/**
 * Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManager implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Format and save the transport manager data
     *
     * @param array $params
     * @return ResponseInterface
     */
    public function process(array $params)
    {
        $data = $params['data'];

        $saved = $this->getServiceLocator()->get('Entity\TransportManager')->save($data);

        if (isset($data['id'])) {
            $id = $data['id'];
        } else {
            $id = $saved['id'];
        }

        return new Response(Response::TYPE_SUCCESS, ['id' => $id]);
    }
}
