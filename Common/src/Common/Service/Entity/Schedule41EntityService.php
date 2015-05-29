<?php

/**
 * Schedule41EntityService.php
 */
namespace Common\Service\Entity;

/**
 * Class Schedule41EntityService
 *
 * Entity service for the Schedule41 entity.
 *
 * @package Common\Service\Entity
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class Schedule41EntityService extends AbstractEntityService
{
    /**
     * Entity reference.
     *
     * @var string
     */
    protected $entity = 's4';

    /**
     * Get all schedule41 records by their application.
     *
     * @param $applicationId
     *
     * @return array
     */
    public function getByApplication($applicationId)
    {
        return $this->getAll(['application' => $applicationId]);
    }
}
