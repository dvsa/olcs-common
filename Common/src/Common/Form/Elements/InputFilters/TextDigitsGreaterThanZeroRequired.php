<?php

/**
 * TextDigitsGreaterThanZeroRequired
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element as ZendElement;
use Laminas\Validator as LaminasValidator;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

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
                ['name' => 'Laminas\Validator\Digits'],
                ['name' => 'Laminas\Validator\GreaterThan', 'options'=>['min' => 0]]
            ]
        ];

        return $specification;
    }
}
