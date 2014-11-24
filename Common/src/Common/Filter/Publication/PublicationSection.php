<?php

/**
 * Publication Section Id filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Publication Section Id filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationSection extends AbstractPublicationFilter
{
    const HEARING_SECTION_ID = 13;

    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $newData = [
            'publicationSection' => self::HEARING_SECTION_ID,
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}