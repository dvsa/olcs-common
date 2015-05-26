<?php

/**
 * Licence filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Licence filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Licence extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $licenceService = $this->getServiceLocator()->get('\Common\Service\Data\Licence');
        $licenceService->setData($licenceService->getId(), null);
        $licence = $licenceService->fetchLicenceData();

        if (!isset($licence['id'])) {
            throw new ResourceNotFoundException('No licence found');
        }

        $newData = [
            'pubType' => $licence['goodsOrPsv']['id'] == self::GV_LIC_TYPE ? 'A&D' : 'N&P',
            'licType' => $licence['goodsOrPsv']['id'],
            'licence' => $licence['id'],
            'trafficArea' => $licence['trafficArea']['id'],
            'licenceData' => $licence
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
