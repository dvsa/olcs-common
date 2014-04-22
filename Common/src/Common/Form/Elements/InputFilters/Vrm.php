<?php

/**
 * Vrm Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\StringLength;
use Zend\I18n\Validator\Alnum;

/**
 * Vrm Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Vrm extends ZendElement implements InputProviderInterface
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
                ['name' => 'Zend\Filter\StringToUpper'],
                [
                    'name' => 'Zend\Filter\PregReplace',
                    'options' => [
                        'pattern' => '/\ /',
                        'replacement' => ''
                    ]
                ]
            ],
            'validators' => [
                new StringLength(['min' => 2, 'max' => 7]),
                new Alnum()
            ]
        ];

        return $specification;
    }
}
