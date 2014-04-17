<?php
namespace Common\Form\Elements\InputFilters;
use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

class TextMax70 extends ZendElement implements InputProviderInterface
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
                ['name' => 'Zend\Filter\StringToLower'],
            ],
            'validators' => [
                new ZendValidator\StringLength(['min' => 2, 'max' => 70]),
            ]
        ];

        return $specification;
    }
}