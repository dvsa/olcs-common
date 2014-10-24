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

    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'convictionDate',
            'convictionCategory',
            'notes',
            'courtFpn',
            'categoryText',
            'penalty',
            'title',
            'forename',
            'familyName'
        )
    );

    public function getDataForApplication($applicationId)
    {
        return $this->get(array('application' => $applicationId), $this->dataBundle)['Results'];
    }

    public function getData($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}
