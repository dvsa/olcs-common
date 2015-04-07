<?php

/**
 * Transport Manager Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Common\Service\Entity\TransportManagerEntityService;

/**
 * Transport Manager Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerApplication implements
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
        $applicationId = $params['applicationId'];

        $userService = $this->getServiceLocator()->get('Entity\User');

        $user = $userService->getUserDetails($userId);

        if ($user['transportManager'] === null) {
            $tmParams = [
                'data' => [
                    'tmStatus' => TransportManagerEntityService::TRANSPORT_MANAGER_STATUS_CURRENT,
                    'homeCd' => $user['contactDetails']['id']
                ]
            ];

            $response = $this->getBusinessServiceManager()->get('Lva\TransportManager')->process($tmParams);

            if (!$response->isOk()) {
                return $response;
            }

            $user['transportManager'] = $response->getData();
        }

        $tmaData = [
            'action' => 'A',
            'application' => $applicationId,
            'transportManager' => $user['transportManager']['id']
        ];

        $saved = $this->getServiceLocator()->get('Entity\TransportManagerApplication')->save($tmaData);

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        $response->setData(['linkId' => $saved['id']]);
        return $response;
    }
}
