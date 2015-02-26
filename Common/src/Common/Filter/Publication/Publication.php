<?php

/**
 * Publication info filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Publication info filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Publication extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $params = [
            'pubType' => $publication->offsetGet('pubType'),
            'trafficArea' => $publication->offsetGet('trafficArea'),
            'pubStatus' => $this->publicationNewStatus
        ];

        $data = $this->getServiceLocator()->get('\Common\Service\Data\Publication')->fetchList($params);

        if (!isset($data[0]['id'])) {
            throw new ResourceNotFoundException('No publication found');
        }

        $newData = [
            'publication' => $data[0]['id'],
            'publicationNo' => $data[0]['publicationNo']
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
