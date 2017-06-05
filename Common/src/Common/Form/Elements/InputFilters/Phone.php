<?php

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\NotEmpty;

/**
 * Phone Filter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Phone extends ZendElement implements InputProviderInterface
{
    protected $required = false;

    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        $this->setAttribute('pattern', '\d(\+|\-|\(|\))*');
        $this->setLabel('contact-number-optional');
        parent::init();
    }

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => $this->required,
            'filters' => [
                ['name' => \Zend\Filter\StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Contact number is missing',
                        ],
                    ],
                    'break_chain_on_failure' => true,
                ],
                [
                    'name'=> \Zend\Validator\Regex::class,
                    'options' => [
                        'pattern' => '/^[0-9 \(\)\-\+]+$/',
                        'messages' => [
                            'regexNotMatch' => 'The input must contain only digits or spaces',
                        ],
                    ],
                ],
                [
                    'name' => \Zend\Validator\StringLength::class,
                    'options' => [
                        'min' => 5,
                        'max' => 45,
                    ],
                ],
            ],
        ];

        return $specification;
    }
}
