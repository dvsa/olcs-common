<?php

/**
 * Hearing date and time publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Hearing date and time publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingDateTime extends AbstractPublicationFilter
{
    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $hearingData = $publication->offsetGet('hearingData');

        $dateTime = new \DateTime($hearingData['hearingDate']);

        $hearingData['date'] = $dateTime->format('j F Y');
        $hearingData['time'] = $dateTime->format('H:i');

        $publication->offsetSet('hearingData', $hearingData);

        return $publication;
    }
}
