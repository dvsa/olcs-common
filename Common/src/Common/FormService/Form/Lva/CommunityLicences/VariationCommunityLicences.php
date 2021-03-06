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
