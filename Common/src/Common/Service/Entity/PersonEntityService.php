<?php

/**
 * Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PersonEntityService extends AbstractEntityService
{
    /**
     * Set a sane limit when fetching people
     */
    const ORG_PERSON_LIMIT = 50;

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Person';

    /**
     * Holds the organisation bundle
     *
     * @var array
     */
    private $organisationBundle = array(
        'properties' => array('position'),
        'children' => array(
            'person' => array(
                //
            )
        )
    );

    private $personBundle = array(
        'properties' => array(
            'version',
            'id',
            'title',
            'forename',
            'familyName',
            'birthDate',
            'otherName'
        )
    );

    public function __construct()
    {
        $this->organisationBundle['children']['person'] = $this->personBundle;
    }

    /**
     * Get all people for a given organisation
     *
     * @param int $orgId
     * @param int $limit
     */
    public function getAllForOrganisation($orgId, $limit = null)
    {
        if ($limit === null) {
            $limit = self::ORG_PERSON_LIMIT;
        }
        $query = array(
            'organisation' => $orgId,
            'limit' => $limit
        );

        // @TODO move to OrganisationPerson, make wrapper call
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('OrganisationPerson', 'GET', $query, $this->organisationBundle);
    }

    /**
     * Get a single person for a given organisation
     *
     * @param int $orgId
     */
    public function getFirstForOrganisation($orgId)
    {
        $results = $this->getAllForOrganisation($orgId, 1);

        if ($results['Count'] !== 0) {
            $data = $results['Results'][0]['person'];
        } else {
            $data = array();
        }
        return $data;
    }

    /**
     * Get a person by ID
     *
     * @param int $id
     */
    public function getById($id)
    {
        return $this->get($id, $this->personBundle);
    }
}
