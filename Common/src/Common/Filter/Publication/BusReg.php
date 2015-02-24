<?php

/**
 * Bus Reg filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * BusReg filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusReg extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $busReg = $this->getServiceLocator()
            ->get('\Generic\Service\Data\BusReg')
            ->fetchOne($publication->offsetGet('busReg'));

        if (!isset($busReg['id'])) {
            throw new ResourceNotFoundException('No bus registration found');
        }

        $newData = [
            'busRegData' => $busReg
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
