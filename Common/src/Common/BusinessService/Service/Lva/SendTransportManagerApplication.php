<?php

/**
 * Send Transport Manager Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;

/**
 * Send Transport Manager Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendTransportManagerApplication implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface,
    BusinessServiceAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return ResponseInterface
     */
    public function process(array $params)
    {
        $userId = $params['userId'];
        $dob = $params['dob'];

        $user = $this->getServiceLocator()->get('Entity\User')->getUserDetails($userId);

        $this->getServiceLocator()->get('Entity\Person')
            ->save(
                [
                    'id' => $user['contactDetails']['person']['id'],
                    'birthDate' => $dob,
                    'version' => $user['contactDetails']['person']['version'],
                ]
            );

        unset($params['dob']);

        return $this->getBusinessServiceManager()->get('Lva\TransportManagerApplication')
            ->process($params);
    }
}
