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
                new ZendValidator\StringLength(8, 8),
                [
                    'name' => 'Alnum',
                    'options' => array(
                        'messages' => array(
                             Alnum::NOT_ALNUM =>
                                'Must be 8 digits; alpha-numeric characters allowed; ' .
                                'no special characters; mandatory when displayed'
                        ),
                    ),
                ]
            ]
        ];

        return $specification;
    }
}
