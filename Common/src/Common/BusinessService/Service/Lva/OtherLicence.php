<?php

/**
 * OtherLicence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * OtherLicence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OtherLicence implements BusinessServiceInterface, ServiceLocatorAwareInterface
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

        $saved = $this->getServiceLocator()->get('Entity\OtherLicence')->save($data);

        if (!empty($data['id'])) {
            $id = $data['id'];
        } else {
            $id = $saved['id'];
        }

        return new Response(Response::TYPE_SUCCESS, ['id' => $id]);
    }
}
