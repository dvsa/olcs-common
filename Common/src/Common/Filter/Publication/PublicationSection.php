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
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
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
