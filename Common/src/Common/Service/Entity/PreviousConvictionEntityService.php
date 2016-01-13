<?php

/**
 * Previous Conviction Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Previous Conviction Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PreviousConvictionEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'PreviousConviction';

    protected $bundle = [
        'children' => [
            'title'
        ]
    ];

    public function getDataForApplication($applicationId)
    {
        return $this->get(array('application' => $applicationId), $this->bundle)['Results'];
    }

    public function getData($id)
    {
        return $this->get($id, $this->bundle);
    }

    /**
     * Get data for tansport manager
     *
     * @param int $transportManagerId
     * @return array
     */
    public function getDataForTransportManager($transportManagerId)
    {
        return $this->get(array('transportManager' => $transportManagerId), $this->bundle)['Results'];
    }
}
