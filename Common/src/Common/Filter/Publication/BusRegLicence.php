<?php

/**
 * Bus Reg Licence filter
 *
 * Similar to regular licence filter except the traffic area and pubType aren't set based on the licence,
 * they will already have been passed in.
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Bus Reg Licence filter
 *
 * Similar to regular licence filter except the traffic area and pubType are not set based on the licence,
 * they will already have been passed in.
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegLicence extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $licence = $this->getServiceLocator()->get('\Common\Service\Data\Licence')->fetchLicenceData();

        if (!isset($licence['id'])) {
            throw new ResourceNotFoundException('No licence found');
        }

        $newData = [
            'licence' => $licence['id'],
            'licenceData' => $licence
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
