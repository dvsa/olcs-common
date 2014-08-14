<?php

/**
 * Gpw Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\GreaterThan;
use Zend\I18n\Validator\Alnum;

/**
 * Gpw Element
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Gpw extends ZendElement implements InputProviderInterface
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
            'validators' => [
                ['name' => 'Zend\Validator\Digits'],
                ['name' => 'Zend\Validator\GreaterThan', 'options' =>['min' => 0]],
            ]
        ];

        return $specification;
    }
}
