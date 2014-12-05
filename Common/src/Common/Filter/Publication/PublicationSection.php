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
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $const = $publication->offsetGet('publicationSectionConst');

        $newData = [
            'publicationSection' => $this->$const,
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
