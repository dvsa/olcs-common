<?php

/**
 * Type Of Licence Adapter Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Type Of Licence Adapter Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface TypeOfLicenceAdapterAwareInterface
{
    /**
     * @return TypeOfLicenceAdapterInterface
     */
    public function getTypeOfLicenceAdapter();

    public function setTypeOfLicenceAdapter(TypeOfLicenceAdapterInterface $adapter);
}
