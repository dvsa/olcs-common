<?php

/**
 * Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

use Common\Exception\DataServiceException;

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
            'person' => array(
                'children' => array(
                    'title'
                )
            )
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
            throw new DataServiceException('Expected to get one organisation person record');
        }

        return $result['Results'][0];
    }

    /**
     * Remove a record by org *and* person ID
     */
    public function deleteByOrgAndPersonId($orgId, $personId)
    {
        $query = array(
            'organisation' => $orgId,
            'person' => $personId
        );

        $this->deleteList($query);

        // delete the actual person row if they no longer relate
        // to an organisation
        $remaining = $this->get(['person' => $personId]);

        if ($remaining['Count'] === 0) {
            $this->getServiceLocator()->get('Entity\Person')->delete($personId);
        }
    }

    /**
     * Get all records for a given organisation
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
