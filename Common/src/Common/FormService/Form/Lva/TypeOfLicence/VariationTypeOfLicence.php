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

        $licenceTypeFieldset = $form->get('type-of-licence')->get('licence-type');

        $this->getFormHelper()->setCurrentOption(
            $licenceTypeFieldset->get('licence-type'),
            $params['currentLicenceType']
        );

        $this->getFormHelper()->setCurrentOption(
            $licenceTypeFieldset->get('ltyp_siContent')->get('vehicle-type'),
            $params['currentVehicleType']
        );
    }

    protected function allElementsLocked(Form $form)
    {
        $this->removeStandardFormActions($form);
        $this->addBackToOverviewLink($form, 'variation');
    }
}
