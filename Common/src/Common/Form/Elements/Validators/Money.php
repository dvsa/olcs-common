<?php

/**
 * Money
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Money
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Money extends AbstractValidator
{
    protected $messageTemplates = [
        'invalid' => 'money-element-invalid'
    ];

    public function isValid($value)
    {
        // None numerics are not allowed
        if (!is_numeric($value)) {
            $this->error('invalid');
            return false;
        }

        // We can allow ints
        if (is_int($value)) {
            return true;
        }

        $value = floatval($value);

        // If we have a float, it can't be more than 2 dp
        if (round($value, 2) == $value) {
            return true;
        }

        $this->error('invalid');
        return false;
    }
}
