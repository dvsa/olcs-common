<?php

/**
 * Application filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Application filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Application extends AbstractPublicationFilter
{
    const GV_LIC_TYPE = 'lcat_gv';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $applicationData = $this->getServiceLocator()
            ->get('Generic\Service\Data\Application')
            ->fetchOne($publication->offsetGet('application'));

        if (!isset($applicationData['id'])) {
            throw new ResourceNotFoundException('No application found');
        }

        $operatingCentres = (isset($applicationData['operatingCentres']) ? $applicationData['operatingCentres'] : []);

        $newData = [
            'applicationData' => $applicationData,
            'operatingCentreData' => $operatingCentres,
            'transportManagerData' => $applicationData['transportManagers']
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
