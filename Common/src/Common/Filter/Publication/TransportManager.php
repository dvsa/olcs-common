<?php

/**
 * Case filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * TransportManager filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TransportManager extends AbstractPublicationFilter
{

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $case = $publication->offsetGet('case');
        $tmId = $case['transportManager']['id'];
        $tmData = $this->getServiceLocator()->get('\Common\Service\Data\TransportManager')->fetchTmData($tmId);

        if (!isset($tmData['id'])) {
            throw new ResourceNotFoundException('No transport manager found');
        }

        $newData = [
            'transportManager' =>
                $tmData['homeCd']['person']['title'] . ' '
                . $tmData['homeCd']['person']['forename'] . ' '
                . $tmData['homeCd']['person']['familyName'],
        ];

        $publication->offsetSet('transportManagerName', $newData['transportManager']);

        return $publication;
    }
}
