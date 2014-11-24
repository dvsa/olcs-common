<?php

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Form\LabelAwareInterface;

/**
 * OlcsCheckbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
class OlcsCheckbox extends ZendElement\Checkbox implements InputProviderInterface, LabelAwareInterface
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
        $labelPosition = $this->getLabelOption('label_position');
        if (empty($labelPosition)) {
            $this->setLabelOption('label_position', \Zend\Form\View\Helper\FormRow::LABEL_APPEND);
        }

        $alwaysWrap = $this->getLabelOption('always_wrap');
        if (empty($alwaysWrap)) {
            $this->setLabelOption('always_wrap', true);
        }

        return [
            'name' => $this->getName(),
            'required' => $this->getOption('required'),
            'validators' => [
                $this->getValidator()
            ]
        ];

    }
}
