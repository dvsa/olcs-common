<?php
namespace Common\Form\Elements\Filter;
use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

class Name extends ZendElement implements InputProviderInterface
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
            'required' => true,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
                ['name' => 'Zend\Filter\StringToLower'],
            ],
            'validators' => [
                new ZendValidator\StringLength(['min' => 2, 'max' => 100]),
            ]
        ];

        return $specification;
    }
}