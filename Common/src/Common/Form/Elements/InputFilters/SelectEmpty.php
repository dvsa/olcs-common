<?php

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Select as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * @deprecated Unused Custom InputFilter.  Need to remove in OLCS-15198
 *
 * Select with empty allowed
 */
class SelectEmpty extends ZendElement implements InputProviderInterface
{
    protected $required = false;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

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
                    'name' => ZendValidator\NotEmpty::class,
                    'options' => [
                        'type' => ZendValidator\NotEmpty::EMPTY_ARRAY,
                    ],
                ],
            ]
        ];

        return $specification;
    }
}
