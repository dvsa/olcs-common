<?php

/**
 * PiVenue publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * PiVenue publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PiVenue extends AbstractPublicationFilter
{
    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     */
    public function filter($publication)
    {
        $hearingData = $publication->offsetGet('hearingData');

        if ((int)$hearingData['piVenue']) {
            $venueDetails = $this->getServiceLocator()
                ->get('DataServiceManager')
                ->get('Common\Service\Data\PiVenue')
                ->fetchById($hearingData['piVenue']);

            $addressFields = [
                'addressLine1',
                'addressLine2',
                'addressLine3',
                'addressLine4',
                'town',
                'postcode'
            ];

            $populatedFields = [
                'name' => $venueDetails['name']
            ];

            foreach ($addressFields as $field) {
                if (isset($venueDetails['address'][$field]) && trim($venueDetails['address'][$field]) != '') {
                    $populatedFields[$field] = $venueDetails['address'][$field];
                }
            }

            //overwrite piVenueOther to save logic over which value to use in the later filters
            $hearingData['piVenueOther'] = implode(', ', $populatedFields);
        }

        $publication->offsetSet('hearingData', $hearingData);

        return $publication;
    }
}
