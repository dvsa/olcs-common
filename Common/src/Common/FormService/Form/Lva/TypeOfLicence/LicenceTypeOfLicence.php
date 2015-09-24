<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\Form\Elements\InputFilters\Lva\BackToLicenceActionLink;
use Zend\Form\Form;

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
        /** @var \Zend\Form\Fieldset $formActions */
        $formActions = $form->get('form-actions');

        foreach ($formActions->getElements() as $name => $element) {
            $formActions->remove($name);
        }

        $formActions->add(new BackToLicenceActionLink());
    }
}
