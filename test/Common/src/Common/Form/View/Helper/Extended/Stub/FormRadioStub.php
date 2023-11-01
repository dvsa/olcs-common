<?php

namespace CommonTest\Form\View\Helper\Extended\Stub;

use Common\Form\View\Helper\Extended\FormRadio;
use Laminas\Form\Element\MultiCheckbox;

class FormRadioStub extends FormRadio
{
    public function renderOptions(MultiCheckbox $element, array $options, array $selectedOptions, array $attributes): string
    {
        return parent::renderOptions($element, $options, $selectedOptions, $attributes);
    }
}
