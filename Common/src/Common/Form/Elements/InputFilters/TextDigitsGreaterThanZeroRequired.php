<?php

/**
 * TextDigitsGreaterThanZeroRequired
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * TextDigitsGreaterThanZeroRequired
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk>
 */
class TextDigitsGreaterThanZeroRequired extends ZendElement implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                new ZendValidator\Digits(),
                new ZendValidator\GreaterThan(array('min' => 0))
            ]
        ];

        return $specification;
    }
}
