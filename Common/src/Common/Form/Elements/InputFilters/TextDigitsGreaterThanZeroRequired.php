<?php

/**
 * TextDigitsGreaterThanZeroRequired
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element as LaminasElement;
use Laminas\Validator as LaminasValidator;
use Laminas\InputFilter\InputProviderInterface;

/**
 * TextDigitsGreaterThanZeroRequired
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk>
 */
class TextDigitsGreaterThanZeroRequired extends LaminasElement implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     */
    public function getInputSpecification(): array
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                ['name' => \Laminas\Validator\Digits::class],
                ['name' => \Laminas\Validator\GreaterThan::class, 'options'=>['min' => 0]]
            ]
        ];
    }
}
