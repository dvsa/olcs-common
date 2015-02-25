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
    const APP_PERSON_LIMIT = 50;

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
            'originalPerson',
            'organisation'
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
            $limit = self::APP_PERSON_LIMIT;
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

    /**
     * Get a record by application *and* original person ID
     *
     * By 'original person' we're referring to a parent record
     * in the person table which this app_org_person represents
     * an update of
     *
     * @param type $applicationId
     * @param type $personId
     */
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

    /**
     * Delete a record by a given application and person ID
     *
     * @NOTE that this only deletes the application_organisation_record, not
     * the person it links to
     */
    public function deleteByApplicationAndPersonId($applicationId, $personId)
    {
        $appPerson = $this->getByApplicationAndPersonId($applicationId, $personId);

        if ($appPerson) {
            $this->delete($appPerson['id']);
        }
    }

    /**
     * Delete a record by a given application and original person ID
     *
     * @NOTE that this deletes both the application_organisation_record
     * and the person it links to, but *not* the 'original' person
     */
    public function deleteByApplicationAndOriginalPersonId($applicationId, $personId)
    {
        $appPerson = $this->getByApplicationAndOriginalPersonId($applicationId, $personId);

        if ($appPerson) {
            $this->deletePerson($appPerson['id'], $appPerson['person']['id']);
        }
    }

    /**
     * Create a variation record of a person
     */
    public function variationCreate($orgId, $applicationId, $data)
    {
        return $this->variationPersist($orgId, $applicationId, $data, 'A');
    }

    /**
     * Update a variation record of a person
     */
    public function variationUpdate($orgId, $applicationId, $data)
    {
        return $this->variationPersist($orgId, $applicationId, $data, 'U');
    }

    /**
     * Delete a variation record of a person
     */
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

    /*
     * Update a person linked to an application
     *
     * @NOTE that if the person has a 'position' field we also
     * persist that, but it has to be stored against the app
     * org record rather than the person themselves
     */
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
