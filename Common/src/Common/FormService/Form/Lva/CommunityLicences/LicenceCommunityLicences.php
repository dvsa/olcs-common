<?php

/**
 * Licence Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\CommunityLicences;

/**
 * Licence Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceCommunityLicences extends AbstractCommunityLicences
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

        $this->removeStandardFormActions($form);

        return $form;
    }
}
