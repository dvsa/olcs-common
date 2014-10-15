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
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'OrganisationPerson';

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
        $result = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', $query);

        return $result['Results'][0];
    }

    public function deleteByOrgAndPersonId($orgId, $personId)
    {
        $query = array(
            'organisation' => $orgId,
            'person' => $personId
        );
        $result = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'DELETE', $query);
    }

    /**
     * Retrieve a record by person ID
     */
    public function getByPersonId($id)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', array('person' => $id));
    }
}
