<?php

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationService extends AbstractEntityService
{
    /**
     * Holds the applications bundle
     *
     * @var array
     */
    private $applicationsForUserBundle = array(
        'properties' => array(),
        'children' => array(
            'organisationUsers' => array(
                'properties' => array(),
                'children' => array(
                    'organisation' => array(
                        'properties' => array(),
                        'children' => array(
                            'licences' => array(
                                'properties' => array(
                                    'id',
                                    'licNo'
                                ),
                                'children' => array(
                                    'applications' => array(
                                        'properties' => array(
                                            'id',
                                            'createdOn',
                                            'receivedDate',
                                            'isVariation'
                                        ),
                                        'children' => array(
                                            'status' => array(
                                                'properties' => array(
                                                    'id'
                                                )
                                            )
                                        )
                                    ),
                                    'licenceType' => array(
                                        'properties' => array(
                                            'id',
                                            'description'
                                        )
                                    ),
                                    'status' => array(
                                        'properties' => array(
                                            'id',
                                            'description'
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Get applications for a given user
     *
     * @param int $userId
     */
    public function getForUser($userId)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall('User', 'GET', $userId, $this->applicationsForUserBundle);
    }
}
