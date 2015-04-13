<?php

/**
 * OcComplaintsEntityService.php
 */
namespace Common\Service\Entity;

/**
 * Class OcComplaintsEntityService
 *
 * @package Common\Service\Entity
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class OcComplaintsEntityService extends AbstractEntityService
{
    /**
     * Entity to referance.
     *
     * @var string
     */
    protected $entity = 'OcComplaint';

    /**
     * Get and return the count for the operating centre complaints.
     *
     * @param null|int $operatingCentreId The operating centre to query.
     *
     * @return int The count based on the bundle.
     */
    public function getCountComplaintsForOpCentre($operatingCentreId = null)
    {
        return $this->getList(['operatingCentre' => $operatingCentreId])['Count'];
    }
}
