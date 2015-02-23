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
            'person',
            'originalPerson'
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

    public function getByApplicationAndOriginalPersonId($applicationId, $personId)
    {
        $query = array(
            'application' => $applicationId,
            'originalPerson' => $personId
        );

        $result = $this->get($query, $this->peopleBundle);

        if ($result['Count'] < 1) {
            return false;
        }

        return $result['Results'][0];
    }

    public function deleteByApplicationAndPersonId($applicationId, $personId)
    {
        $appPerson = $this->getByApplicationAndPersonId($applicationId, $personId);

        if ($appPerson) {
            $this->delete($appPerson['id']);
        }
    }

    public function deleteByApplicationAndOriginalPersonId($applicationId, $personId)
    {
        $appPerson = $this->getByApplicationAndOriginalPersonId($applicationId, $personId);

        if ($appPerson) {
            $this->deletePerson($appPerson['id'], $appPerson['person']['id']);
        }
    }

    public function variationCreate($personId, $orgId, $applicationId)
    {
        return $this->variationAction($personId, $orgId, $applicationId, 'A');
    }

    public function variationUpdate($personId, $orgId, $applicationId, $originalId)
    {
        return $this->variationAction($personId, $orgId, $applicationId, 'U', $originalId);
    }

    public function variationDelete($personId, $orgId, $applicationId)
    {
        return $this->variationAction($personId, $orgId, $applicationId, 'D');
    }

    private function variationAction($personId, $orgId, $applicationId, $action, $originalId = null)
    {
        $data = [
            'action' => $action,
            'organisation' => $orgId,
            'application' => $applicationId,
            'person' => $personId
        ];

        if (isset($originalId)) {
            $data['originalPerson'] = $originalId;
        }

        return $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')->save($data);
    }

    public function deletePerson($id, $personId)
    {
        $this->delete($id);
        $this->getServiceLocator()->get('Entity\Person')->delete($personId);
    }
}
