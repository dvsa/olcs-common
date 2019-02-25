<?php

namespace Common\Form\Input;

use Zend\InputFilter\Input;
use Zend\Validator\NotEmpty;

class RequiredValidationInput extends Input
{
    protected $isEmptyMessage = 'Value is required and can\'t be empty';

    protected function prepareRequiredValidationFailureMessage()
    {
        return [
            NotEmpty::IS_EMPTY => $this->isEmptyMessage
        ];
    }
}
