<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Laminas\Form\Form;

/**
 * Variation Type Of Licence
 */
class VariationTypeOfLicence extends AbstractTypeOfLicence
{
    protected function alterForm(Form $form, $params = [])
    {
        parent::alterForm($form, $params);

        $this->getFormServiceLocator()->get('lva-variation')->alterForm($form);

        $this->lockElements($form, $params);

        $this->getFormHelper()->setCurrentOption(
            $form->get('type-of-licence')->get('licence-type')->get('licence-type'),
            $params['currentLicenceType']
        );
    }

    protected function allElementsLocked(Form $form)
    {
        $this->removeStandardFormActions($form);
        $this->addBackToOverviewLink($form, 'variation');
    }
}
