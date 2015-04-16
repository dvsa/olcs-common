<?php

/**
 * Tm Employment
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\ContactDetailsEntityService;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;

/**
 * Tm Employment
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TmEmployment implements BusinessServiceInterface, ServiceLocatorAwareInterface, BusinessServiceAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $employment = $params['data'];
        $address = $params['address'];

        $response = $this->getBusinessServiceManager()->get('Lva\Address')->process(['data' => $address]);

        if (!$response->isOk()) {
            return $response;
        }

        if (!isset($address['id']) || empty($address['id'])) {
            $addressId = $response->getData()['id'];
            $contactDetails = [
                'data' => [
                    'address' => $addressId,
                    'contactType' => ContactDetailsEntityService::CONTACT_TYPE_TRANSPORT_MANAGER
                ]
            ];
            $response = $this->getBusinessServiceManager()->get('Lva\ContactDetails')->process($contactDetails);

            if (!$response->isOk()) {
                return $response;
            }

            $employment['contactDetails'] = $response->getData()['id'];
        }

        $saved = $this->getServiceLocator()->get('Entity\TmEmployment')->save($employment);

        if (!empty($employment['id'])) {
            $id = $employment['id'];
        } else {
            $id = $saved['id'];
        }

        return new Response(Response::TYPE_SUCCESS, ['id' => $id]);
    }
}
