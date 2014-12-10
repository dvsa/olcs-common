<?php

/**
 * Type Of Licence Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Type Of Licence Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface TypeOfLicenceAdapterInterface
{
    public function getQueryParams();

    public function getRouteParams();

    public function doesChangeRequireConfirmation(array $postData, array $currentData);

    public function processChange(array $postData, array $currentData);

    public function processFirstSave($applicationId);
}
