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
     * Organisation type keys
     */
    const ORG_TYPE_PARTNERSHIP = 'org_t_p';
    const ORG_TYPE_OTHER = 'org_t_pa';
    const ORG_TYPE_REGISTERED_COMPANY = 'org_t_rc';
    const ORG_TYPE_LLP = 'org_t_llp';
    const ORG_TYPE_SOLE_TRADER = 'org_t_st';

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
     * Bundle to retrieve data to update completion status
     *
     * @var array
     */
    private $businessDetailsBundle = array(
        'children' => array(
            'type' => array(
                'properties' => array(
                    'id'
                )
            ),
            'tradingNames' => array(
                'properties' => array(
                    'id',
                    'name'
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

    /**
     * Get business details data
     *
     * @param type $id
     */
    public function getBusinessDetailsData($id)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'GET', $id, $this->businessDetailsBundle);
    }
}
