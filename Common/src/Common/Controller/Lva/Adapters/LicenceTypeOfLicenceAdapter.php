<?php

/**
 * Licence Type Of Licence Adapter
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
 * Licence Type Of Licence Adapter
 * @NOTE This is a CONTROLLER adapter and thus contains logic similar to that of a controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTypeOfLicenceAdapter implements
    TypeOfLicenceAdapterInterface,
    ServiceLocatorAwareInterface,
    ControllerAwareInterface
{
    use ServiceLocatorAwareTrait,
        ControllerAwareTrait;

    public function getQueryParams()
    {

    }

    public function getRouteParams()
    {

    }

    public function doesChangeRequireConfirmation(array $postData, array $currentData)
    {
        return false;
    }

    public function processChange(array $postData, array $currentData)
    {
        return false;
    }

    public function processFirstSave($applicationId)
    {

    }

    public function alterForm(\Zend\Form\Form $form)
    {
        $form->get('form-actions')->get('save')->setLabel('Save');

        return $form;
    }

    public function setMessages()
    {
        // @todo check some stuff, as the message is different

        // If some fields are editable
        $translationHelper = $this->getServiceLocator()->get('Helper\Translation');

        $message = $translationHelper->formatTranslation(
            '%s <a href="%s" target="_blank">%s</a>',
            array(
                'variation-application-text2',
                // @todo replace with real link
                'https://www.google.co.uk/?q=Licence+Type#q=Licence+Type',
                'variation-application-link-text'

            )
        );

        $this->getServiceLocator()->get('Helper\FlashMessenger')
            ->addInfoMessage($message);
    }
}
