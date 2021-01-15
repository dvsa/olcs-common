<?php

namespace Common\Service\Gds;

use Common\Form\View\Helper\ApplicationContext;
use Laminas\Form\ElementInterface;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;

class FormUpdater
{
    public function __construct($applicationContext)
    {
        $this->applicationContext = $applicationContext;
    }

    public function update(Form $form)
    {
        if ($this->applicationContext != ApplicationContext::APPLICATION_CONTEXT_SELFSERVE) {
            return;
        }

        foreach ($form->getFieldsets() as $fieldset) {
            $this->processFieldset($fieldset);
        }
    }

    private function processFieldset(Fieldset $fieldset)
    {
        foreach ($fieldset->getElements() as $element) {
            $this->processElement($element);
        }

        foreach ($fieldset->getFieldsets() as $fieldset) {
            $this->processFieldset($fieldset);
        }
    }

    private function processElement(ElementInterface $element)
    {
        if ($element instanceof Checkbox) {
            $this->processCheckbox($element);
        } elseif ($element instanceof MultiCheckbox) {
            $this->processMultiCheckbox($element);
        }
    }

    private function processCheckbox(Checkbox $checkbox)
    {
        $checkbox->setAttribute('class', 'govuk-checkboxes__input');

        $labelAttributes = $checkbox->getLabelAttributes();
        $labelAttributes['class'] = 'govuk-label govuk-checkboxes__label';
        $checkbox->setLabelAttributes($labelAttributes);
    }

    private function processMultiCheckbox(MultiCheckbox $multiCheckbox)
    {
        $multiCheckbox->setAttribute('class', 'govuk-checkboxes__input');

        $labelAttributes = $checkbox->getLabelAttributes();
        $labelAttributes['class'] = 'govuk-label govuk-checkboxes__label';
        $multiCheckbox->setLabelAttributes($labelAttributes);
    }
}
