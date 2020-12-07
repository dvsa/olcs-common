<?php

/**
 * Application Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\CommunityLicences;

/**
 * Application Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationCommunityLicences extends AbstractCommunityLicences
{
    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeFormAction($form, 'save');
        $this->removeFormAction($form, 'cancel');

        return $form;
    }
}
