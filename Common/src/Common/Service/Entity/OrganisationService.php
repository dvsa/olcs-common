<?php

/**
 * Organisation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Organisation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Organisation';

    /**
     * Holds the organisation bundle
     *
     * @var array
     */
    private $organisationFromUserBundle = array(
        'properties' => array(),
        'children' => array(
            'organisation' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Holds the organisation type bundle
     *
     * @var array
     */
    private $typeBundle = array(
        'properties' => array(
            'type',
            'version'
        ),
        'children' => array(
            'type' => array(
                'properties' => array(
                    'id'
                )
            )
        )
    );

    /**
     * Get the organisation for the given user
     *
     * @param int $userId
     */
    public function getForUser($userId)
    {
        $organisation = $this->getHelperService('RestHelper')
            ->makeRestCall('OrganisationUser', 'GET', ['user' => $userId], $this->organisationFromUserBundle);

        if ($organisation['Count'] < 1) {
            throw new \Exception('Organisation not found');
        }

        return $organisation['Results'][0]['organisation'];
    }

    /**
     * Get type of organisation
     *
     * @param int $id
     * @return array
     */
    public function getType($id)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'GET', $id, $this->typeBundle);
    }
}
