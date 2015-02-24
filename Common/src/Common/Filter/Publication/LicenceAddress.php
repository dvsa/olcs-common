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
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licenceData = $publication->offsetGet('licenceData');

        if (!empty($licenceData['correspondenceCd'])) {

            $addressFields = [
                'addressLine1',
                'addressLine2',
                'addressLine3',
                'addressLine4',
                'town',
                'postcode'
            ];

            $populatedFields = [];
            $address = $licenceData['correspondenceCd']['address'];

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
