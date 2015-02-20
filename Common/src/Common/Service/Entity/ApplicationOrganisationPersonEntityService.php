<?php

/**
 * Application Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Application Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOrganisationPersonEntityService extends AbstractEntityService
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
    protected $entity = 'ApplicationOrganisationPerson';

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
     * Get all people for a given application
     *
     * @param int $applicationId
     * @param int $limit
     */
    public function getAllByApplication($applicationId, $limit = null)
    {
        if ($limit === null) {
            $limit = self::ORG_PERSON_LIMIT;
        }

        $query = array(
            'application' => $applicationId,
            'limit' => $limit
        );

        return $this->get($query, $this->peopleBundle);
    }

    /**
     * Get a record by application *and* person ID
     *
     * @param type $applicationId
     * @param type $personId
     */
    public function getByApplicationAndPersonId($applicationId, $personId)
    {
        $query = array(
            'application' => $applicationId,
            'person' => $personId
        );

        $result = $this->get($query);

        if ($result['Count'] < 1) {
            return false;
        }

        return $result['Results'][0];
    }

    public function variationCreate($personId, $orgId, $applicationId)
    {
        return $this->variationAction($personId, $orgId, $applicationId, 'A');
    }

    public function variationDelete($personId, $orgId, $applicationId)
    {
        return $this->variationAction($personId, $orgId, $applicationId, 'D');
    }

    private function variationAction($personId, $orgId, $applicationId, $action)
    {
        $data = [
            'action' => $action,
            'organisation' => $orgId,
            'application' => $applicationId,
            'person' => $personId
        ];

        return $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')->save($data);
    }
}
