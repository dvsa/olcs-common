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
     * Retrieve all company subs for a given licence
     *
     * @param int $id
     * @return array
     */
    public function getForLicence($id)
    {
        return $this->getAll(['licence' => $id]);
    }
}
