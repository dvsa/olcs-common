<?php

/**
 * Vehicle History View Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Vehicle History View Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleHistoryViewEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'VehicleHistoryView';

    public function getDataForVrm($vrm)
    {
        return $this->getAll(
            array(
                'vrm' => $vrm,
                'sort' => 'specifiedDate',
                'order' => 'DESC'
            )
        );
    }
}
