<?php

/**
 * Generic Business Details
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Traits;

use Common\Service\Entity\AddressEntityService;

/**
 * Generic Business Details
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait GenericBusinessDetails
{
    /**
     * Save the nature of business
     *
     * @param int $orgId
     * @param array $natureOfBusiness
     */
    private function saveNatureOfBusiness($orgId, $natureOfBusiness = [])
    {
        $organisationNatureOfBusinessService = $this->getServiceLocator()->get('Entity\OrganisationNatureOfBusiness');
        $existingRecords = $organisationNatureOfBusinessService->getAllForOrganisation($orgId);
        $formattedExistingRecords = [];
        foreach ($existingRecords as $record) {
            $formattedExistingRecords[] = $record['refData']['id'];
        }
        $recordsToInsert = array_diff($natureOfBusiness, $formattedExistingRecords);
        $recordsToDelete = array_diff($formattedExistingRecords, $natureOfBusiness);

        $organisationNatureOfBusinessService->deleteByOrganisationAndIds($orgId, $recordsToDelete);

        foreach ($recordsToInsert as $id) {
            $natureOfBusiness = [
                'organisation' => $orgId,
                'refData' => $id,
                'createdBy' => $this->getLoggedInUser()
            ];
            $organisationNatureOfBusinessService->save($natureOfBusiness);
        }
    }

    /**
     * Save the organisations registered address
     *
     * @param int $orgId
     * @param array $address
     */
    private function saveRegisteredAddress($orgId, $address)
    {
        $saved = $this->getServiceLocator()->get('Entity\Address')->save($address);

        // If we didn't have an address id, then we need to link it to the organisation
        if (!isset($address['id']) || empty($address['id'])) {
            $contactDetailsData = array(
                'organisation' => $orgId,
                'address' => $saved['id'],
                'contactType' => AddressEntityService::CONTACT_TYPE_REGISTERED_ADDRESS
            );

            $this->getServiceLocator()->get('Entity\ContactDetails')->save($contactDetailsData);
        }
    }

    /**
     * User has pressed 'Find company' on registered company number
     * 
     * @param array $data
     * @param Zend\Form\Form $form
     * @param string $fieldset
     */
    private function processCompanyLookup($data, $form, $fieldset)
    {
        $this->getServiceLocator()
            ->get('Helper\Form')
            ->processCompanyNumberLookupForm($form, $data, $fieldset);
    }
}
