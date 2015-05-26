<?php

/**
 * Operating Centre Address filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Operating Centre Address filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class OperatingCentreAddress extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $address = $publication->offsetGet('operatingCentreAddressData');

        if (!empty($address)) {

            $addressFields = [
                'addressLine1',
                'addressLine2',
                'addressLine3',
                'addressLine4',
                'town',
                'postcode'
            ];

            $populatedFields = [];

            foreach ($addressFields as $field) {
                if (isset($address[$field]) && trim($address[$field]) != '') {
                    $populatedFields[$field] = $address[$field];
                }
            }

            $publication->offsetSet('operatingCentreAddress', implode(', ', $populatedFields));
        }

        return $publication;
    }
}
