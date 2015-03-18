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
                'address' => $saved['id'],
                'contactType' => AddressEntityService::CONTACT_TYPE_REGISTERED_ADDRESS
            );

            $saved = $this->getServiceLocator()->get('Entity\ContactDetails')->save($contactDetailsData);
            return $saved['id'];
        }
    }
}
