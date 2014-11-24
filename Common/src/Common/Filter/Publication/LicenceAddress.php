<?php

/**
 * Licence Address filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Licence Address filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceAddress extends AbstractPublicationFilter
{
    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     */
    public function filter($publication)
    {
        $licenceData = $publication->offsetGet('licenceData');

        if (!empty($licenceData['contactDetails'])) {

            $addressFields = [
                'addressLine1',
                'addressLine2',
                'addressLine3',
                'addressLine4',
                'town',
                'postcode'
            ];

            $populatedFields = [];
            $address = $licenceData['contactDetails'][0]['address'];

            foreach ($addressFields as $field) {
                if (isset($address[$field]) && trim($address[$field]) != '') {
                    $populatedFields[$field] = $address[$field];
                }
            }

            $publication->offsetSet('licenceAddress', implode(', ', $populatedFields));
        }

        return $publication;
    }
}
