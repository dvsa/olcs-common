<?php

/**
 * Variation Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\CommunityLicences;

/**
 * Variation Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationCommunityLicences extends AbstractCommunityLicences
{
    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
