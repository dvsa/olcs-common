<?php

/**
 * Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OrganisationPersonEntityService extends AbstractEntityService
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
    protected $entity = 'OrganisationPerson';

    /**
     * Holds the organisation people
     *
     * @var array
     */
    private $peopleBundle = array(
        'children' => array(
            'person'
        )
    );

    /**
     * Get a record by org *and* person ID
     *
     * @param type $orgId
     * @param type $personId
     */
    public function getByOrgAndPersonId($orgId, $personId)
    {
        $query = array(
            'organisation' => $orgId,
            'person' => $personId
        );

        $result = $this->get($query);

        if ($result['Count'] < 1) {
            throw new Exceptions\UnexpectedResponseException('Expected to get one organisation person record');
        }

        return $result['Results'][0];
    }

    public function deleteByOrgAndPersonId($orgId, $personId)
    {
        $query = array(
            'organisation' => $orgId,
            'person' => $personId
        );

        $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'DELETE', $query);
    }

    /**
     * Retrieve a record by person ID
     */
    public function getByPersonId($id)
    {
        $orgPerson = $this->get(array('person' => $id), $this->peopleBundle);
        if ($orgPerson['Count'] === 0) {
            return false;
        }
        return $orgPerson['Results'][0];
    }

    /**
     * Get all people for a given organisation
     *
     * @param int $orgId
     * @param int $limit
     */
    public function getAllByOrg($orgId, $limit = null)
    {
        if ($limit === null) {
            $limit = self::ORG_PERSON_LIMIT;
        }

        $query = array(
            'organisation' => $orgId,
            'limit' => $limit
        );

        return $this->get($query, $this->peopleBundle);
    }
}
