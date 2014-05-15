<?php

/**
 * CompanyNumber fieldset
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Zend\Form\Element\Text;
use Zend\Form\Element\Button;
use Zend\Form\Element\Select;

/**
 * CompanyNumber fieldset
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class CompanyNumber extends Fieldset
{

    /**
     * Setup the elements
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {


        parent::__construct($name, $options);

        $companyNumber = new Text('company_number');
        $companyNumber->setAttributes(
            array(
                'class' => 'short',
                'data-container-class' => 'inline'
            )
        );

        $this->add($companyNumber);

        $searchButton = new Button('submit_lookup_company', array('label' => 'Find company'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--secondary large'
            )
        );
        $searchButton->setValue('submit_lookup_company');

        $this->add($searchButton);
        $this->setAttribute('class', 'highlight-box');

        $text = new \Common\Form\Elements\Types\PlainText('desc');
        $text->setValue("If you don't have your company number to hand, or are having problems retrieving your company details, please enter them manually below.");
        $this->add($text);

    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
