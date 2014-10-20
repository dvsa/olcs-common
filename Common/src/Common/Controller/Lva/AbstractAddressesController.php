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
        ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE => 'correspondence',
        ContactDetailsEntityService::CONTACT_TYPE_ESTABLISHMENT => 'establishment',
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
        $data = array(
            'id' => $data['correspondence']['id'],
            'version' => $data['correspondence']['version'],
            'fao' => $data['correspondence']['fao'],
            'contactType' => ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE,
            'licence' => $this->getLicenceId(),
            'emailAddress' => $data['contact']['email'],
            'addresses' => array(
                'address' => $data['correspondence_address'],
            )
        );

        return $this->getServiceLocator()->get('Entity\ContactDetails')->save($data);
    }

    protected function maybeSaveEstablishmentAddress($data)
    {
        if (!empty($data['establishment'])) {
            $address = array(
                'id' => $data['establishment']['id'],
                'version' => $data['establishment']['version'],
                'contactType' => ContactDetailsEntityService::CONTACT_TYPE_ESTABLISHMENT,
                'licence' => $this->getLicenceId(),
                'addresses' => array(
                    'address' => $data['establishment_address'],
                )
            );

            $this->getServiceLocator()->get('Entity\ContactDetails')->save($address);
        }
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    private function alterForm($form)
    {
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

        $contactDetailsMerge = array_merge($data['contactDetails'], $data['organisation']['contactDetails']);

        foreach ($contactDetailsMerge as $contactDetails) {

            if (!isset($contactDetails['contactType']['id'])) {
                continue;
            }

            $type = $this->mapFormTypeFromDbType($contactDetails['contactType']['id']);

            $returnData[$type] = array(
                'id' => $contactDetails['id'],
                'version' => $contactDetails['version'],
                'fao' => $contactDetails['fao']
            );

            $returnData[$type . '_address'] = $contactDetails['address'];
            $returnData[$type . '_address']['countryCode'] = $contactDetails['address']['countryCode']['id'];

            if ($contactDetails['contactType']['id'] == ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE) {

                $returnData['contact']['email'] = $contactDetails['emailAddress'];

                foreach ($contactDetails['phoneContacts'] as $phoneContact) {

                    $phoneType = $this->mapFormTypeFromDbType($phoneContact['phoneContactType']['id']);

                    $returnData['contact'][$phoneType] = $phoneContact['phoneNumber'];
                    $returnData['contact'][$phoneType . '_id'] = $phoneContact['id'];
                    $returnData['contact'][$phoneType . '_version'] = $phoneContact['version'];
                }
            }
        }

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
