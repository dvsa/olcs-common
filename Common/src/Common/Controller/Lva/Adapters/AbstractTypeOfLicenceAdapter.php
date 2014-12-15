<?php

/**
 * Abstract Type Of Licence Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface;
use Common\Controller\Lva\Interfaces\ControllerAwareInterface;
use Common\Controller\Lva\Traits\ControllerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Type Of Licence Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceAdapter implements
    TypeOfLicenceAdapterInterface,
    ServiceLocatorAwareInterface,
    ControllerAwareInterface
{
    use ServiceLocatorAwareTrait,
        ControllerAwareTrait;

    protected $queryParams = [];
    protected $confirmationMessage;
    protected $extraConfirmationMessage;

    public function getConfirmationMessage()
    {
        return $this->confirmationMessage;
    }

    public function getExtraConfirmationMessage()
    {
        return $this->extraConfirmationMessage;
    }

    public function getQueryParams()
    {
        return ['query' => $this->queryParams];
    }

    public function getRouteParams()
    {
        return ['action' => 'confirmation'];
    }

    public function alterForm(\Zend\Form\Form $form, $id = null, $applicationType = null)
    {
        return $form;
    }

    public function setMessages($id = null, $applicationType = null)
    {

    }

    public function processChange(array $postData, array $currentData)
    {
        return false;
    }

    public function processFirstSave($applicationId)
    {

    }

    public function isCurrentDataSet($currentData)
    {
        return !empty($currentData['niFlag']) && !empty($currentData['goodsOrPsv'])
            && !empty($currentData['licenceType']);
    }
}
