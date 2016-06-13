<?php

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Checkbox extends ZendElement\Checkbox
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
        $options = $this->getOptions();

        if (!isset($options['must_be_value'])
            || $options['must_be_value'] === false
            || $options['must_be_value'] === null) {
            return array();
        }

        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                [
                    'name' => 'Identical',
                    'options' => array(
                        'token' => $options['must_be_value'],
                        'messages' => array(
                            ZendValidator\Identical::NOT_SAME =>
                                isset($this->getOptions()['not_checked_message'])
                                    ? $this->getOptions()['not_checked_message']
                                    : 'common.form.validation.checkbox.not_same',
                        ),
                    ),
                ]
            ]
        ];

        return $specification;
    }
}
