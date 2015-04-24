<?php

/**
 * ChangeOfEntityEntityService.php
 */
namespace Common\Service\Entity;

/**
 * Class ChangeOfEntityEntityService
 *
 * Change of entity entity service, facilitates retrieving the change of entity records.
 *
 * @package Common\Service\Entity
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class ChangeOfEntityEntityService extends AbstractEntityService
{
    /**
     * Entity reference.
     *
     * @var string
     */
    protected $entity = 'ChangeOfEntity';

    /**
     * Get a change of entity records by its licence.
     *
     * @param $licenceId
     *
     * @return array
     */
    public function getForLicence($licenceId)
    {
        return $this->get(['licence' => $licenceId]);
    }
}
