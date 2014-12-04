<?php

/**
 * Type Of Licence Validation Adapter Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Common\Controller\Lva\Interfaces\TypeOfLicenceValidationAdapterInterface;

/**
 * Type Of Licence Validation Adapter Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait TypeOfLicenceValidationAdapterAwareTrait
{
    protected $typeOfLicenceValidationAdapter;

    /**
     * @return TypeOfLicenceValidationAdapterInterface
     */
    public function getTypeOfLicenceValidationAdapter()
    {
        return $this->typeOfLicenceValidationAdapter;
    }

    public function setTypeOfLicenceValidationAdapter(TypeOfLicenceValidationAdapterInterface $adapter)
    {
        $this->typeOfLicenceValidationAdapter = $adapter;
    }
}
