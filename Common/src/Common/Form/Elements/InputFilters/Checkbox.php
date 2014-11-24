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
use Zend\Form\LabelAwareInterface;

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Checkbox extends ZendElement\Checkbox implements InputProviderInterface, LabelAwareInterface
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

        $labelPosition = $this->getLabelOption('label_position');
        if (empty($labelPosition)) {
            $this->setLabelOption('label_position', \Zend\Form\View\Helper\FormRow::LABEL_APPEND);
        }

        $alwaysWrap = $this->getLabelOption('always_wrap');
        if (empty($alwaysWrap)) {
            $this->setLabelOption('always_wrap', true);
        }

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
                                    : 'You must check this box to continue',
                        ),
                    ),
                ]
            ]
        ];

        return $specification;
    }
}
