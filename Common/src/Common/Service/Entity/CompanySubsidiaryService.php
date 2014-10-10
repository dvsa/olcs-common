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

    private $companyBundle = array(
        'properties' => array(
            'id',
            'version',
            'name',
            'companyNo'
        )
    );

    public function getAllForOrganisation($id)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall(
                $this->entity,
                'GET',
                array('organisation' => $id),
                $this->companyBundle
            );
    }
}
