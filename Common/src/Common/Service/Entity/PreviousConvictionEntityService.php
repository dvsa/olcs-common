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

    public function getDataForApplication($applicationId)
    {
        return $this->get(array('application' => $applicationId))['Results'];
    }

    public function getData($id)
    {
        return $this->get($id);
    }
}
