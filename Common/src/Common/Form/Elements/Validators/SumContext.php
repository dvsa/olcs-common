<?php

/**
 * Sum Context
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Sum Context - Checks that the sum of all context values is within a configured range
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SumContext extends AbstractValidator
{
    const BELOW_MIN = 'belowMin';
    const ABOVE_MAX = 'aboveMax';

    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var array
     */
    protected $messageVariables = [
        'min' => 'min',
        'max' => 'max'
    ];

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::BELOW_MIN => 'The sum of all values must be greater than %min%',
        self::ABOVE_MAX => 'The sum of all values must be less than %max%'
    ];

    /**
     * @param $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @param $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @param  mixed $value
     * @param  array $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        unset($value); // Removes CS violation

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
