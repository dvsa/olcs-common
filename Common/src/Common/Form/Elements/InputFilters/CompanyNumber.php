<?php

/**
 * Company Number
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\I18n\Validator\Alnum;

/**
 * Company Number
 *
 * @author Someone <someone@valtech.co.uk>
 */
class CompanyNumber extends ZendElement implements InputProviderInterface
{
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
                new Alnum(),
                new ZendValidator\StringLength(8, 8)
            ]
        ];

        return $specification;
    }
}
