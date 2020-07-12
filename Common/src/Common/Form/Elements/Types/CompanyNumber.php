<?php

/**
 * CompanyNumber
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;

/**
 * CompanyNumber
 *
 * @author Someone <someone@valtech.co.uk>
 */
class CompanyNumber extends Fieldset
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setAttribute('class', 'lookup');

        $this->add(
            array(
                'type' => 'Common\Form\Elements\Types\PlainText',
                'name' => 'description',
                'attributes' => [
                    'data-container-class' => 'hint',
                ],
                'options' => [
                    'value' => 'selfserve-business-registered-company-description'
                ]
            )
        );

        $this->add(
            array(
                'type' => 'Common\Form\Elements\InputFilters\CompanyNumber',
                'name' => 'company_number',
                'attributes' => [
                    'data-container-class' => 'inline',
                    'pattern' => '\d*'
                ],
            )
        );

        $this->add(
            array(
                'type' => 'button',
                'name' => 'submit_lookup_company',
                'options' => [
                    'label' => 'Find company',
                ],
                'attributes' => [
                    'class' => 'action--primary',
                    'data-container-class' => 'inline',
                    'type' => 'submit',
                ],
            )
        );
    }


    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages($elementName = null)
    {
        return current($this->messages);
    }
}
