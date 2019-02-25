<?php

namespace Common\Form\Input;

use Zend\InputFilter\Input;
use Zend\Validator\NotEmpty;

class SectorsInput extends Input
{
    protected function prepareRequiredValidationFailureMessage()
    {
        return [
            NotEmpty::IS_EMPTY => 'error.messages.sector.list'
        ];
    }
}
