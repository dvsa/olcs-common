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
use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Transport Manager Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerApplicationForUser implements
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

            $userData = [
                'id' => $user['id'],
                'version' => $user['version'],
                'transportManager' => $user['transportManager']['id']
            ];

            // Update the user record, so we can link them to the Transport Manager record
            $this->getServiceLocator()->get('Entity\User')->save($userData);
        }

        $tmaData = [
            'tmApplicationStatus' => TransportManagerApplicationEntityService::STATUS_INCOMPLETE,
            'action' => 'A',
            'application' => $applicationId,
            'transportManager' => $user['transportManager']['id']
        ];

        /* @var $response Response */
        $tmaResponse = $this->getBusinessServiceManager()
            ->get('Lva\TransportManagerApplication')
            ->process(['data' => $tmaData]);
        if (!$tmaResponse->isOk()) {
            return $tmaResponse;
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        $response->setData(['linkId' => $tmaResponse->getData()['id']]);

        return $response;
    }
}
