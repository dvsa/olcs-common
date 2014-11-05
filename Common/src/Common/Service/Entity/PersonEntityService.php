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
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Person';

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

    /**
     * Get all people for a given organisation
     *
     * @param int $orgId
     * @param int $limit
     */
    public function getAllForOrganisation($orgId, $limit = null)
    {
        return $this->getServiceLocator()
            ->get('Entity\OrganisationPerson')->getAllByOrg($orgId, $limit);
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
