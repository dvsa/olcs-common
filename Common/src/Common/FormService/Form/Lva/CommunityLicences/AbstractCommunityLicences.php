<?php

/**
 * Community Licences
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva\CommunityLicences;

use Common\FormService\Form\Lva\AbstractLvaFormService;

/**
 * Community Licences
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractCommunityLicences extends AbstractLvaFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\CommunityLicences');

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        return $form;
    }
}
