<?php

/**
 * Abstract Type Of Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\Form\AbstractFormService;
use Zend\Form\Form;
use Common\RefData;

/**
 * Abstract Type Of Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicence extends AbstractFormService
{
    public function getForm($params = [])
    {
        $form = $this->getFormHelper()->createForm('Lva\TypeOfLicence');

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm(Form $form, $params = [])
    {
        // no op
    }

    protected function lockElements(Form $form, $params = [])
    {
        $typeOfLicenceFieldset = $form->get('type-of-licence');

        // Change labels
        $typeOfLicenceFieldset->get('operator-location')->setLabel('operator-location');
        $typeOfLicenceFieldset->get('operator-type')->setLabel('operator-type');
        $typeOfLicenceFieldset->get('licence-type')->setLabel('licence-type');

        // Add padlocks
        $this->getFormHelper()->lockElement(
            $typeOfLicenceFieldset->get('operator-location'),
            'operator-location-lock-message'
        );
        $this->getFormHelper()->lockElement(
            $typeOfLicenceFieldset->get('operator-type'),
            'operator-type-lock-message'
        );

        // Disable elements
        $this->getFormHelper()->disableElement($form, 'type-of-licence->operator-location');
        $this->getFormHelper()->disableElement($form, 'type-of-licence->operator-type');

        // Optional disable and lock type of licence
        if (!$params['canUpdateLicenceType']) {
            // Disable and lock type of licence
            $this->getFormHelper()->disableElement($form, 'type-of-licence->licence-type');
            $this->getFormHelper()->lockElement(
                $typeOfLicenceFieldset->get('licence-type'),
                'licence-type-lock-message'
            );

            // Disable buttons
            $this->getFormHelper()->disableElement($form, 'form-actions->save');
        }

        if (!$params['canBecomeSpecialRestricted']) {
            $this->getFormHelper()->removeOption(
                $typeOfLicenceFieldset->get('licence-type'),
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
            );
        }
    }
}
