<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\Form\Lva\AbstractLvaFormService;
use Common\RefData;
use Laminas\Form\Form;

/**
 * Abstract Type Of Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicence extends AbstractLvaFormService
{
    const ALLOWED_OPERATOR_LOCATION_NI = 'NI';
    const ALLOWED_OPERATOR_LOCATION_GB = 'GB';

    /**
     * Get Form
     *
     * @param array $params parameters
     *
     * @return Form
     */
    public function getForm($params = [])
    {
        $form = $this->getFormHelper()->createForm('Lva\TypeOfLicence');

        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Make changed in form
     *
     * @param Form  $form   Form
     * @param array $params parameters
     *
     * @return void
     */
    protected function alterForm(Form $form, $params = [])
    {
        // no op
    }

    /**
     * Make action when all elements are locked
     *
     * @param Form $form Form
     *
     * @return void
     */
    protected function allElementsLocked(Form $form)
    {
        // no op
    }

    /**
     * Lock Elements
     *
     * @param Form  $form   Form
     * @param array $params parameters
     *
     * @return void
     */
    protected function lockElements(Form $form, $params = [])
    {
        /** @var \Laminas\Form\Fieldset $typeOfLicenceFieldset */
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

            $this->allElementsLocked($form);
        }

        if (!$params['canBecomeSpecialRestricted']) {
            $this->getFormHelper()->removeOption(
                $typeOfLicenceFieldset->get('licence-type'),
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
            );
        }
    }

    /**
     * Set and lock operator location
     *
     * @param Form   $form     Form
     * @param string $location Operator Location Code
     *
     * @return void
     */
    public function setAndLockOperatorLocation($form, $location)
    {
        /** @var \Laminas\Form\Fieldset $typeOfLicenceFieldset */
        $typeOfLicenceFieldset = $form->get('type-of-licence');

        $elmOperLoc = $typeOfLicenceFieldset->get('operator-location');

        $message = null;
        if ($location === self::ALLOWED_OPERATOR_LOCATION_NI) {
            $elmOperLoc->setValue('Y');
            $message = 'alternative-operator-location-lock-message-ni';
        } elseif ($location === self::ALLOWED_OPERATOR_LOCATION_GB) {
            $elmOperLoc->setValue('N');
            $message = 'alternative-operator-location-lock-message-gb';
        }

        $formHelper = $this->getFormHelper();
        $formHelper->disableElement($form, 'type-of-licence->operator-location');
        $formHelper->lockElement($elmOperLoc, $message);
    }

    /**
     * Alter form for NI applications
     *
     * @param Form $form Form
     *
     * @return void
     */
    public function maybeAlterFormForNi($form)
    {
        if ($form->get('type-of-licence')->get('operator-location')->getValue() === 'Y') {
            $form->getInputFilter()->get('type-of-licence')->get('operator-type')->setRequired(false);
        }
    }
}
