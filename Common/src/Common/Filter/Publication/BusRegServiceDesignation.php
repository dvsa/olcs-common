<?php

/**
 * Bus Registration Service Designation filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Service Designation filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegServiceDesignation extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busReg = $publication->offsetGet('busRegData');

        $services = [$busReg['serviceNo']];

        if (!empty($busReg['otherServices'])) {
            foreach ($busReg['otherServices'] as $otherService) {
                $services[] = $otherService['serviceNo'];
            }
        }

        $newData = [
            'busServices' => implode(' / ', $services),
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
