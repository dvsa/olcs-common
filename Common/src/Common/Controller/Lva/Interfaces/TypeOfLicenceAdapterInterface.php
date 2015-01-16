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
interface TypeOfLicenceAdapterInterface extends AdapterInterface
{
    public function getQueryParams();

    public function getRouteParams();

    public function doesChangeRequireConfirmation(array $postData, array $currentData);

    public function processChange(array $postData, array $currentData);

    public function processFirstSave($applicationId);

    public function alterForm(\Zend\Form\Form $form, $id = null, $applicationType = null);

    public function setMessages($id = null, $applicationType = null);

    public function getConfirmationMessage();

    public function getExtraConfirmationMessage();

    public function confirmationAction();

    public function isCurrentDataSet($currentData);
}
