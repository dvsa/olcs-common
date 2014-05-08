<?php

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Checkbox extends ZendElement\Checkbox implements InputProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

    /**
     * Provide default input rules for checkbox element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        if (!isset($this->getOptions()['must_be_checked']) || $this->getOptions()['must_be_checked'] == false) {
            return array();
        }

        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                [
                    'name' => 'Identical',
                    'options' => array(
                        'token' => '1',
                        'messages' => array(
                             ZendValidator\Identical::NOT_SAME =>
                                isset($this->getOptions()['not_checked_message'])
                                    ? $this->getOptions()['not_checked_message']
                                    : 'You must agree',
                        ),
                    ),
                ]
            ]
        ];

        return $specification;
    }
}
