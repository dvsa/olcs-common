<?php

/**
 * Multi checkbox with empty allowed
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Validator\NotEmpty;
use Zend\Form\Element\MultiCheckbox;
use Zend\InputFilter\InputProviderInterface;

/**
 * Multi checkbox with empty allowed
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class MultiCheckboxEmpty extends MultiCheckbox implements InputProviderInterface
{
    protected $required = false;

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'required' => $this->required,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    [
                        NotEmpty::NULL
                    ]
                ],
            ],
        ];

        return $specification;
    }
}
