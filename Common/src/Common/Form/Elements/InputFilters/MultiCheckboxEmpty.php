<?php

namespace Common\Form\Elements\InputFilters;

use Laminas\Validator\NotEmpty;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\InputFilter\InputProviderInterface;

/**
 * @deprecated This only gets used once in \Olcs\Controller\Document\DocumentGenerationController
 *             We must look into removing it and replacing with standard MultiCheckbox.
 *             Reference: OLCS-15198
 *
 *
 * Multi checkbox with empty allowed
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
            'required'   => $this->required,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'type' => NotEmpty::NULL,
                    ],
                ],
            ],
        ];

        return $specification;
    }
}
