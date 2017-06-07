<?php

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Hidden as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * @deprecated This should not be used and must be removed as part of OLCS-15198
 *             Replace other elements with the normal Text element provided by
 *             Zend.
 *
 * Custom Hidden Element
 */
class Hidden extends ZendElement implements InputProviderInterface
{
    protected $required = false;
    protected $max = null;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

    /**
     * @param int $max Max string length
     *
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
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
                ['name' => 'Zend\Filter\StringTrim']
            ],
            'validators' => [
                [
                    'name' => ZendValidator\NotEmpty::class,
                    'options' => [
                        'type' => ZendValidator\NotEmpty::NULL,
                    ],
                ],
            ],
        ];

        if (!empty($this->max)) {
            $specification['validators'][] = [
                'name' => 'Zend\Validator\StringLength',
                'options' => ['min' => 2, 'max' => $this->max]
            ];
        }

        return $specification;
    }
}
