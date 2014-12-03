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
class CompanySubsidiaryEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'CompanySubsidiary';

    /**
     * Retrieve all company subs by organisation ID
     *
     * @param int $id
     * @return array
     */
    public function getAllForOrganisation($id)
    {
        return $this->get(array('organisation' => $id));
    }

    /**
     * Retrieve a company subsidiary by ID
     *
     * @param int $id
     * @return array
     */
    public function getById($id)
    {
        return $this->get($id);
    }
}
