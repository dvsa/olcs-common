<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Laminas\Form\Form;

/**
 * Licence Type Of Licence
 */
class LicenceTypeOfLicence extends AbstractTypeOfLicence
{
    protected function alterForm(Form $form, $params = [])
    {
        parent::alterForm($form, $params);

        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        $this->lockElements($form, $params);
    }

    protected function allElementsLocked(Form $form)
    {
        $this->removeStandardFormActions($form);
        $this->addBackToOverviewLink($form, 'licence');
    }
}
