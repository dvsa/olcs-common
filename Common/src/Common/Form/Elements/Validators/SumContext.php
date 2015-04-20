<?php

/**
 * Sum Context
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Sum Context
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SumContext extends AbstractValidator
{
    const BELOW_MIN = 'belowMin';
    const ABOVE_MAX = 'aboveMax';

    protected $min;

    protected $max;

    protected $messageVariables = [
        'min' => 'min',
        'max' => 'max'
    ];

    protected $messageTemplates = [
        self::BELOW_MIN => 'The sum of all values must be greater than %min%',
        self::ABOVE_MAX => 'The sum of all values must be less than %max%'
    ];

    public function setMin($min)
    {
        $this->min = $min;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function isValid($value, $context = null)
    {
        $sum = array_sum($context);

        $valid = true;

        if ($this->min !== null && $sum < $this->min) {
            $valid = false;
            $this->error(self::BELOW_MIN);
        }

        if ($this->max !== null && $sum > $this->max) {
            $valid = false;
            $this->error(self::ABOVE_MAX);
        }

        return $valid;
    }
}
