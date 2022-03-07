<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\RefData;
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

    /**
     * {@inheritdoc}
     */
    protected function lockElements(Form $form, $params = [])
    {
        parent::lockElements($form, $params);

        $typeOfLicenceFieldset = $form->get('type-of-licence');

        if (!$params['canBecomeStandardInternational']) {
            $this->getFormHelper()->disableOption(
                $typeOfLicenceFieldset->get('licence-type')->get('licence-type'),
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
            );
        }
    }

    protected function allElementsLocked(Form $form)
    {
        $this->removeStandardFormActions($form);
        $this->addBackToOverviewLink($form, 'variation');
    }
}
