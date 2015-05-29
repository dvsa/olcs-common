<?php

/**
 * Application Publication Section Id filter
 * NOT CURRENTLY USED AS WE'RE NOW GOING TO BE PASSING IN A PUBLICATION SECTION
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Application Publication Section Id filter
 * NOT CURRENTLY USED AS WE'RE NOW GOING TO BE PASSING IN A PUBLICATION SECTION
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationPublicationSection extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $applicationData = $publication->offsetGet('applicationData');

        switch ($applicationData['status']['id']) {
            case self::APP_NEW_STATUS:
                $section = self::APP_NEW_SECTION;
                break;
            case self::APP_GRANTED_STATUS:
                $section = self::APP_GRANTED_SECTION;
                break;
            case self::APP_REFUSED_STATUS:
                $section = self::APP_REFUSED_SECTION;
                break;
            case self::APP_WITHDRAWN_STATUS:
                $section = self::APP_WITHDRAWN_SECTION;
                break;
            default:
                $section = false;
        }

        if (!$section) {
            throw new ResourceNotFoundException('Could not match to a publication section');
        }

        $newData = [
            'publicationSection' => $section,
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
