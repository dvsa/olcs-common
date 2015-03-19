<?php

/**
 * Registered Address
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\AddressEntityService;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;

/**
 * Registered Address
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RegisteredAddress implements BusinessServiceInterface, BusinessServiceAwareInterface, ServiceLocatorAwareInterface
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
        $orgId = $params['orgId'];
        $address = $params['registeredAddress'];

        $responseData = [];

        $responseData['hasChanged'] = $this->getServiceLocator()->get('Entity\Organisation')
            ->hasChangedRegisteredAddress($orgId, $address);

        $saved = $this->getServiceLocator()->get('Entity\Address')->save($address);

        if (!isset($address['id']) || empty($address['id'])) {
            $responseData['addressId'] = $saved['id'];
        } else {
            $responseData['addressId'] = $address['id'];
        }

        // If we didn't have an address id, then we need to create a contact details record too
        if (!isset($address['id']) || empty($address['id'])) {
            $contactDetailsParams = [
                'data' => [
                    'address' => $saved['id'],
                    'contactType' => AddressEntityService::CONTACT_TYPE_REGISTERED_ADDRESS
                ]
            ];

            $response = $this->getBusinessServiceManager()->get('Lva\ContactDetails')->process($contactDetailsParams);

            if ($response->getType() !== Response::TYPE_PERSIST_SUCCESS) {
                return $response;
            }

            $responseData['contactDetailsId'] = $response->getData()['id'];
        }

        $response = new Response();
        $response->setType(Response::TYPE_PERSIST_SUCCESS);
        $response->setData($responseData);

        return $response;
    }
}
