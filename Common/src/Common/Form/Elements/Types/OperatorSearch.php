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
 * Defendant fieldset
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class OperatorSearch extends Fieldset
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

        $operatorSearch = new Text('operatorSearch');
        $operatorSearch->setAttributes(
            array(
                'class' => 'short',
                'data-container-class' => 'inline'
            )
        );

        $this->add($operatorSearch);

        $searchButton = new Button('search', array('label' => 'Find operator'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--secondary small'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);

        $selectList = new Select('entity-list', array('label' => '', 'empty_option' => 'Please select'));
        $selectList->setAttributes(
            array(
                'data-container-class' => 'inline'
            )
        );

        $this->add($selectList);

        $selectButton = new Button('select', array('label' => 'Select'));
        $selectButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary'
            )
        );
        $selectButton->setValue('select');

        $this->add($selectButton);

        $operatorName = new \Common\Form\Elements\InputFilters\Name('operatorName', array('label' => 'operatorName'));
        $operatorName->setAttributes(
            array(
                'id' => 'operatorName',
                'class' => 'long',
                'placeholder' => ''
            )
        );
        $this->add($operatorName);

        $addNewButton = new Button('addNew', array('label' => 'Add new'));
        $addNewButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--secondary small'
            )
        );
        $addNewButton->setValue('addNew');

        $this->add($addNewButton);
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
