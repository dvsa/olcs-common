<?php

/**
 * Application Tracking Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * Application Tracking Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationTrackingEntityService extends AbstractEntityService
{
    const STATUS_NOT_SET        = 0;

    const STATUS_ACCEPTED       = 1;

    const STATUS_NOT_ACCEPTED   = 2;

    const STATUS_NOT_APPLICABLE = 3;

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'ApplicationTracking';

    /**
     * Get tracking statuses
     *
     * @param int $applicationId
     * @return array
     */
    public function getTrackingStatuses($applicationId)
    {
        $data = $this->get(array('application' => $applicationId));

        if ($data['Count'] < 1) {
            throw new Exceptions\UnexpectedResponseException('Tracking status not found');
        }

        if ($data['Count'] > 1) {
            throw new Exceptions\UnexpectedResponseException('Too many tracking statuses found');
        }

        return $data['Results'][0];
    }

    /**
     * Get options for dropdown
     * (It's not really worth a separate data service for this)
     */
    public function getValueOptions()
    {
        return [
            self::STATUS_NOT_SET        => '',
            self::STATUS_ACCEPTED       => 'Accepted',
            self::STATUS_NOT_ACCEPTED   => 'Not accepted',
            self::STATUS_NOT_APPLICABLE => 'Not applicable',
        ];
    }
}
