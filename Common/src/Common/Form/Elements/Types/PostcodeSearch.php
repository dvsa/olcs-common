<?php

/**
 * PostcodeSearch fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Zend\Form\Element\Text;
use Zend\Form\Element\Button;
use Zend\Form\Element\Select;

/**
 * PostcodeSearch fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PostcodeSearch extends Fieldset
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

        $postcodeSearch = new Text('postcode');
        $postcodeSearch->setAttributes(
            array(
                'class' => 'short',
                'data-container-class' => 'inline'
            )
        );

        $this->add($postcodeSearch);

        $searchButton = new Button('search', array('label' => 'Find address'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--secondary large'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);

        $selectAddress = new Select('addresses', array('label' => '', 'empty_option' => 'Please select'));
        $selectAddress->setAttributes(
            array(
                'data-container-class' => 'inline'
            )
        );

        $this->add($selectAddress);

        $selectButton = new Button('select', array('label' => 'Select'));
        $selectButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary'
            )
        );
        $selectButton->setValue('select');

        $this->add($selectButton);
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
