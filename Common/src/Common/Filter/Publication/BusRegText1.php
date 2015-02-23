<?php

/**
 * Bus Registration Text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegText1 extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busReg = $publication->offsetGet('busRegData');

        $newData = [
            'text1' => $busReg['regNo'],
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
