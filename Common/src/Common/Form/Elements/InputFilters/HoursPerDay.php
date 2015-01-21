<?php

/**
 * Hours per day
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator as ZendValidator;

/**
 * Hours per day
 */
class HoursPerDay extends Text implements InputProviderInterface
{
    protected $allowEmpty = true;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new ZendValidator\Between(
                [
                    'min' => 0,
                    'max' => 24,
                    'messages' => [
                        ZendValidator\Between::NOT_BETWEEN =>
                        ucfirst(substr($this->getName(), -3)) . " must be between '%min%' and '%max%', inclusively"
                    ]
                ]
            )
        );
    }
}
