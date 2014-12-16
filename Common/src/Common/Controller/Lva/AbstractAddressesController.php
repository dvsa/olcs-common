<?php

/**
 * Shared logic between Addresses controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ContactDetailsEntityService;

/**
 * Shared logic between Addresses controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAddressesController extends AbstractController
{
    /**
     * Type map
     *
     * @var array
     */
    protected $typeMap = array(
        'phone_t_tel' => 'phone_business',
        'phone_t_home' => 'phone_home',
        'phone_t_mobile' => 'phone_mobile',
        'phone_t_fax' => 'phone_fax'
    );

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
     * Addresses section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $addressData = $this->getServiceLocator()->get('Entity\Licence')->getAddressesData(
                $this->getLicenceId()
            );

            $data = $this->formatDataForForm($addressData);
        }

        $form = $this->alterForm($this->getAddressesForm())->setData($data);

        $hasProcessed = $this->getServiceLocator()->get('Helper\Form')->processAddressLookupForm($form, $request);

        if (!$hasProcessed && $request->isPost() && $form->isValid()) {

            $correspondenceDetails = $this->saveCorrespondenceDetails($data);

            $correspondenceId = isset($correspondenceDetails['id'])
                ? $correspondenceDetails['id']
                : $data['correspondence']['id'];

            $this->savePhoneNumbers($data, $correspondenceId);

            $this->maybeSaveEstablishmentAddress($data, 'establishment');

            $this->postSave('addresses');

            return $this->completeSection('addresses');
        }

        return $this->render('addresses', $form);
    }

    /**
     * Save phone numbers
     *
     * @param array $data
     * @param int $correspondenceId
     */
    private function savePhoneNumbers($data, $correspondenceId)
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
    private function saveCorrespondenceDetails($data)
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

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    private function alterForm($form)
    {
        $this->alterFormForLva($form);

        $allowedLicTypes = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        $typeOfLicence = $this->getTypeOfLicenceData();

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if (!in_array($typeOfLicence['licenceType'], $allowedLicTypes)) {
            $formHelper->remove($form, 'establishment')
                ->remove($form, 'establishment_address');
        }

        return $form;
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @return array
     */
    private function formatDataForForm($data)
    {
        $returnData = array(
            'contact' => array(
                'phone-validator' => true
            )
        );

        if (!empty($data['correspondenceCd'])) {
            $returnData = $this->formatAddressDataForForm($returnData, $data, 'correspondence');
            $returnData['contact']['email'] = $data['correspondenceCd']['emailAddress'];

            foreach ($data['correspondenceCd']['phoneContacts'] as $phoneContact) {

                $phoneType = $this->mapFormTypeFromDbType($phoneContact['phoneContactType']['id']);

                $returnData['contact'][$phoneType] = $phoneContact['phoneNumber'];
                $returnData['contact'][$phoneType . '_id'] = $phoneContact['id'];
                $returnData['contact'][$phoneType . '_version'] = $phoneContact['version'];
            }
        }

        if (!empty($data['establishmentCd'])) {
            $returnData = $this->formatAddressDataForForm($returnData, $data, 'establishment');
        }

        return $returnData;
    }

    protected function formatAddressDataForForm($returnData, $data, $type)
    {
        $address = $data[$type . 'Cd'];

        $returnData[$type] = array(
            'id' => $address['id'],
            'version' => $address['version'],
            'fao' => $address['fao']
        );

        $returnData[$type . '_address'] = $address['address'];
        $returnData[$type . '_address']['countryCode'] = $address['address']['countryCode']['id'];

        return $returnData;
    }

    /**
     * Map form type from db type
     *
     * @param string $type
     */
    private function mapFormTypeFromDbType($type)
    {
        return (isset($this->typeMap[$type]) ? $this->typeMap[$type] : '');
    }

    /**
     * Get the form
     *
     * @return \Zend\Form\Form
     */
    private function getAddressesForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\Addresses');
    }
}
