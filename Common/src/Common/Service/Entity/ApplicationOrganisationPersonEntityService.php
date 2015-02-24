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

    public function variationCreate($orgId, $applicationId, $data)
    {
        return $this->variationPersist($orgId, $applicationId, $data, 'A');
    }

    public function variationUpdate($orgId, $applicationId, $data)
    {
        return $this->variationPersist($orgId, $applicationId, $data, 'U');
    }

    public function variationDelete($personId, $orgId, $applicationId)
    {
        $data = [
            'action' => 'D',
            'organisation' => $orgId,
            'application' => $applicationId,
            'person' => $personId
        ];

        return $this->save($data);
    }

    private function variationPersist($orgId, $applicationId, $data, $action)
    {
        $variationData = [
            'action' => $action,
            'organisation' => $orgId,
            'application' => $applicationId,
            'originalPerson' => isset($data['id']) ? $data['id'] : null,
            'position' => isset($data['position']) ? $data['position'] : null
        ];

        unset($data['id']);
        $newPerson = $this->getServiceLocator()->get('Entity\Person')->save($data);

        $variationData['person'] = $newPerson['id'];

        return $this->save($variationData);
    }

    public function updatePerson($appData, $personData)
    {
        if (isset($personData['position'])) {
            $appData = [
                'id' => $appData['id'],
                'version' => $appData['version'],
                'position' => $personData['position']
            ];
            // @TODO getting version conflicts without force here; surely the version
            // is fine?
            $this->forceUpdate($appData['id'], $appData);
        }
        return $this->getServiceLocator()->get('Entity\Person')->save($personData);
    }

    /**
     * Deletes not only the app org person but the linked
     * person entity too
     */
    public function deletePerson($id, $personId)
    {
        $this->delete($id);
        $this->getServiceLocator()->get('Entity\Person')->delete($personId);
    }
}
