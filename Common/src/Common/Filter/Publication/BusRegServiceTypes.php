<?php

/**
 * Bus Registration Service Types filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Service Types filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegServiceTypes extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busReg = $publication->offsetGet('busRegData');

        $serviceTypes = [];

        if (!empty($busReg['busServiceTypes'])) {
            foreach ($busReg['busServiceTypes'] as $serviceType) {
                $serviceTypes[] = $serviceType['description'];
            }
        }

        $newData = [
            'busServiceTypes' => implode(', ', $serviceTypes),
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
