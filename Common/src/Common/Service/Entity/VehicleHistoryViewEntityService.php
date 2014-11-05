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

    protected $dataBundle = array(
        'properties' => array(
            'id',
            'vrm',
            'licenceNo',
            'specifiedDate',
            'removalDate',
            'discNo'
        )
    );

    public function getDataForVrm($vrm)
    {
        return $this->get(
            array(
                'vrm' => $vrm,
                'sort' => 'specifiedDate',
                'order' => 'DESC'
            ),
            $this->dataBundle
        );
    }
}
