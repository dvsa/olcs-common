<?php

/**
 * Community Lic Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Community Lic Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommunityLicEntityService extends AbstractEntityService
{
    protected $entity = 'CommunityLic';

    const STATUS_PENDING = 'cl_sts_pending';
    const STATUS_VALID = 'cl_sts_valid';
    const STATUS_EXPIRED = 'cl_sts_expired';
    const STATUS_WITHDRAWN = 'cl_sts_withdrawn';
    const STATUS_SUSPENDED = 'cl_sts_suspended';
    const STATUS_VOID = 'cl_sts_void';
    const STATUS_RETURNDED = 'cl_sts_returned';

    protected $listBundle = array(
        'children' => array(
            'status'
        )
    );

    public function getPendingForLicence($licenceId)
    {
        $query = array(
            'licence' => $licenceId,
            'specifiedDate' => 'NULL',
            'status' => self::STATUS_PENDING
        );

        return $this->getAll($query, $this->listBundle)['Results'];
    }
}
