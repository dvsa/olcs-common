<?php

/**
 * Delete Other Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Delete Other Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteOtherLicence implements
    BusinessServiceInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessServiceAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $ids = $params['ids'];

        foreach ($ids as $id) {
            $this->getServiceLocator()->get('Entity\OtherLicence')->delete($id);
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
