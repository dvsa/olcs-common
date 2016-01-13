<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $data = $params['data'];

        $data['data']['id'] = $params['id'];

        $this->getServiceLocator()->get('Entity\Application')->save($data['data']);

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
