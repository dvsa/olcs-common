<?php
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

class Phone extends ZendElement implements InputProviderInterface
{
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
            'name' => $this->getName(),
            'required' => false,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
            ],
            'validators' => [
                new ZendValidator\Regex([
                    'pattern' => '/^[0-9 ]+$/',
                    'messages' => ['regexNotMatch' => 'The input must contain only digits or spaces']
                ]),
                new ZendValidator\StringLength(['min' => 5, 'max' => 20]),
            ]
        ];

        return $specification;
    }
}
