<?php
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

class CompanyNumber extends ZendElement implements InputProviderInterface
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
            ],
            'validators' => [
                new ZendValidator\StringLength(8, 8),
                new ZendValidator\Digits(),
            ]
        ];

        return $specification;
    }
}
