<?php

/**
 * Company Subsidiary Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Company Subsidiary Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CompanySubsidiaryService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'CompanySubsidiary';

    /**
     * Company bundle
     *
     * @var array
     */
    private $companyBundle = array(
        'properties' => array(
            'id',
            'version',
            'name',
            'companyNo'
        )
    );

    /**
     * Retrieve all company subs by organisation ID
     *
     * @param int $id
     * @return array
     */
    public function getAllForOrganisation($id)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', array('organisation' => $id), $this->companyBundle);
    }

    /**
     * Retrieve a company subsidiary by ID
     *
     * @param int $id
     * @return array
     */
    public function getById($id)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', $id, $this->companyBundle);
    }
}
