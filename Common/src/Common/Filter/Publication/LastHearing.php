<?php

/**
 * Hearing filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * LastHearing filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LastHearing extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $params = [
            'pi' => $publication->offsetGet('pi'),
            'sort' => 'id',
            'order' => 'DESC',
            'limit' => 1
        ];

        $hearingData = $this->getServiceLocator()
            ->get('DataServiceManager')
            ->get('\Common\Service\Data\PiHearing')
            ->fetchList($params);

        if (!isset($hearingData['Results'][0])) {
            throw new ResourceNotFoundException('No hearing found');
        }

        //put pi venue id into the necessary format
        $hearingData['Results'][0]['piVenue'] = $hearingData['Results'][0]['piVenue']['id'];

        //adjust adjourned and cancelled to match
        $newData = [
            'hearingData' => $hearingData['Results'][0]
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
