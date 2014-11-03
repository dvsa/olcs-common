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
     * @param type $orgId
     */
    public function getAllForOrganisation($orgId)
    {
        $query = array(
            'organisation' => $orgId,
            'limit' => self::ORG_PERSON_LIMIT
        );

        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('OrganisationPerson', 'GET', $query, $this->organisationBundle);
    }

    /**
     * Get a person by ID
     *
     * @param type $id
     */
    public function getById($id)
    {
        return $this->get($id, $this->personBundle);
    }
}
