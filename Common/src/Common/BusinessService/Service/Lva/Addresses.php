<?php

/**
 * Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\Service\Entity\ContactDetailsEntityService;
use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;
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
    BusinessRuleAwareInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessRuleAwareTrait,
        ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Phone types
     *
     * @var array
     */
    protected $phoneTypes = array(
        'business' => 'phone_t_tel',
        'home' => 'phone_t_home',
        'mobile' => 'phone_t_mobile',
        'fax' => 'phone_t_fax'
    );

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $data = $params['data'];

        $correspondenceDetails = $this->saveCorrespondenceDetails($data);

        $correspondenceId = isset($correspondenceDetails['id'])
            ? $correspondenceDetails['id']
            : $data['correspondence']['id'];

        $this->savePhoneNumbers($data, $correspondenceId);

        $this->maybeSaveEstablishmentAddress($data, 'establishment');

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);

        return $response;
    }

    /**
     * Save phone numbers
     *
     * @param array $data
     * @param int $correspondenceId
     */
    protected function savePhoneNumbers($data, $correspondenceId)
    {
        foreach ($this->phoneTypes as $phoneType => $phoneRefName) {

            $phone = array(
                'id' => $data['contact']['phone_' . $phoneType . '_id'],
                'version' => $data['contact']['phone_' . $phoneType . '_version'],
            );

            if (!empty($data['contact']['phone_' . $phoneType])) {

                $phone['phoneNumber'] = $data['contact']['phone_' . $phoneType];
                $phone['phoneContactType'] = $phoneRefName;
                $phone['contactDetails'] = $correspondenceId;

                $this->getServiceLocator()->get('Entity\PhoneContact')->save($phone);

            } elseif ((int)$phone['id'] > 0) {
                $this->getServiceLocator()->get('Entity\PhoneContact')->delete($phone['id']);
            }
        }
    }

    /**
     * Save correspondence details
     *
     * @param array $data
     * @return array
     */
    protected function saveCorrespondenceDetails($data)
    {
        return $this->saveAddressToLicence(
            $data,
            ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE,
            'correspondence',
            [
                'fao' => $data['correspondence']['fao'],
                'emailAddress' => $data['contact']['email'],
            ]
        );
    }

    protected function maybeSaveEstablishmentAddress($data)
    {
        if (!empty($data['establishment'])) {

            return $this->saveAddressToLicence(
                $data,
                ContactDetailsEntityService::CONTACT_TYPE_ESTABLISHMENT,
                'establishment'
            );
        }
    }

    protected function saveAddressToLicence($data, $contactType, $type, $additionalData = array())
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

        $saved = $this->getServiceLocator()->get('Entity\ContactDetails')->save($address);

        // If we are creating a new contactDetails item, we need to link it to the licence
        if (!isset($data[$type]['id']) || empty($data[$type]['id'])) {

            $licenceData = [$type . 'Cd' => $saved['id']];

            $this->getServiceLocator()->get('Entity\Licence')->forceUpdate($this->getLicenceId(), $licenceData);
        }

        return $saved;
    }
}
