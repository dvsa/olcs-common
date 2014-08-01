<?php

/**
 * PersonSearch fieldset
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Zend\Form\Element\Text;
use Zend\Form\Element\Button;
use Zend\Form\Element\Select;

/**
 * PersonSearch fieldset
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class PersonSearch extends Fieldset
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

        $personSearch = new Text('personSearch');
        $personSearch->setAttributes(
            array(
                'class' => 'short',
                'data-container-class' => 'inline'
            )
        );

        $this->add($personSearch);

        $searchButton = new Button('search', array('label' => 'Find person'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--secondary large'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);

        $selectPerson = new Select('person-list', array('label' => '', 'empty_option' => 'Please select'));
        $selectPerson->setAttributes(
            array(
                'data-container-class' => 'inline'
            )
        );

        $this->add($selectPerson);

        $selectButton = new Button('select', array('label' => 'Select'));
        $selectButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary'
            )
        );
        $selectButton->setValue('select');

        $this->add($selectButton);

        $personFirstname = new \Common\Form\Elements\InputFilters\Name('personFirstname', array('label' => 'First name'));
        $personFirstname->setAttributes(
            array(
                'id' => 'personFirstname',
                'class' => 'long',
                'placeholder' => ''
            )
        );
        $this->add($personFirstname);

        $personLastname = new \Common\Form\Elements\InputFilters\Name('personLastname', array('label' => 'Last name'));
        $personLastname->setAttributes(
            array(
                'id' => 'personLastname',
                'class' => 'long',
                'placeholder' => ''
            )
        );

        $this->add($personLastname);

        $dateOfBirth = new \Common\Form\Elements\Custom\DateSelect(
            'dateOfBirth',
             array('label' => 'Date of birth')
        );
        $dateOfBirth->setAttributes(
            array(
                'id' => 'dob',
                'class' => 'long',
            )
        );
        $dateOfBirth->setOptions(
            [
                'create_empty_option' => true,
                'render_delimiters' => false,
                'required' => false,
            ]
        );
        $this->add($dateOfBirth);

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
