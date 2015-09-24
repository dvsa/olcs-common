<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\Form\Elements\InputFilters\Lva\BackToApplicationActionLink;
use Zend\Form\Form;

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
            $form->get('type-of-licence')->get('licence-type'),
            $params['currentLicenceType']
        );
    }

    protected function allElementsLocked(Form $form)
    {
        /** @var \Zend\Form\Fieldset $formActions */
        $formActions = $form->get('form-actions');

        foreach ($formActions->getElements() as $name => $element) {
            $formActions->remove($name);
        }

        $formActions->add(new BackToApplicationActionLink());
    }
}
