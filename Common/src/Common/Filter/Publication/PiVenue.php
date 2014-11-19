<?php

/**
 * PiVenue publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

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
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $hearingData = $publication->offsetGet('hearingData');

        if ((int)$hearingData['piVenue']) {
            $hearingData['piVenueOther'] = 'Venue details, from venues service';
        }

        $publication->offsetSet('hearingData', $hearingData);

        return $publication;
    }
}