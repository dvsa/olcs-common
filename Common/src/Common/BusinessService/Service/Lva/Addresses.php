<?php

/**
 * Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\Service\Entity\ContactDetailsEntityService;
use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Addresses implements
    BusinessServiceInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
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
        $data = $params['data'];
        $licenceId = $params['licenceId'];

        $response = $this->saveCorrespondenceDetails($licenceId, $data);

        if (!$response->isOk()) {
            return $response;
        }

        $correspondenceDetails = $response->getData();

        $correspondenceId = isset($correspondenceDetails['id'])
            ? $correspondenceDetails['id']
            : $data['correspondence']['id'];

        $response = $this->getBusinessServiceManager()
            ->get('Lva\PhoneContact')
            ->process(
                [
                    'data' => $data,
                    'correspondenceId' => $correspondenceId
                ]
            );

        if (!$response->isOk()) {
            return $response;
        }

        if (!empty($data['establishment'])) {
            $response = $this->saveAddressToLicence(
                $licenceId,
                $data,
                ContactDetailsEntityService::CONTACT_TYPE_ESTABLISHMENT,
                'establishment'
            );

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);

        return $response;
    }

    /**
     * Save correspondence details
     *
     * @param array $data
     * @return array
     */
    protected function saveCorrespondenceDetails($licenceId, $data)
    {
        return $this->saveAddressToLicence(
            $licenceId,
            $data,
            ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE,
            'correspondence',
            [
                'fao' => $data['correspondence']['fao'],
                'emailAddress' => $data['contact']['email'],
            ]
        );
    }

    protected function saveAddressToLicence($licenceId, $data, $contactType, $type, $additionalData = array())
    {
        $address = array(
            'id' => $data[$type]['id'],
            'version' => $data[$type]['version'],
            'contactType' => $contactType,
            'addresses' => array(
                'address' => $data[$type . '_address'],
            )
        );

        $address = array_merge($address, $additionalData);

        $response = $this->getServiceLocator()
            ->get('BusinessServiceManager')
            ->get('Lva\ContactDetails')
            ->process(
                [
                    'data' => $address
                ]
            );

        if (!$response->isOk()) {
            return $response;
        }

        // If we are creating a new contactDetails item, we need to link it to the licence
        if (!isset($data[$type]['id']) || empty($data[$type]['id'])) {
            $saved = $response->getData();

            $licenceData = [$type . 'Cd' => $saved['id']];

            $this->getServiceLocator()->get('Entity\Licence')->forceUpdate($licenceId, $licenceData);
        }

        return $response;
    }
}
