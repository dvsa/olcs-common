<?php

/**
 * ApplicationTransportManagers fieldset
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Zend\Form\Element\Text;
use Zend\Form\Element\Button;
use Zend\Form\Element\Select;
use Common\Form\Elements\Types\HtmlTranslated;

/**
 * ApplicationTransportManagers fieldset
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ApplicationTransportManagers extends Fieldset
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

        $applicationSearch = new Text('application');
        $applicationSearch->setAttributes(
            array(
                'class' => 'short js-input',
                'data-container-class' => 'inline'
            )
        );
        $applicationSearch->setOption('remove_if_readonly', true);

        $this->add($applicationSearch);

        $searchButton = new Button('search', array('label' => 'Find application'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary large js-find',
                'data-container-class' => 'inline'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);

        $transportManager = new Select('transportManager', array('label' => '', 'empty_option' => 'Please select'));
        $transportManager->setAttributes(
            array(
                'data-container-class' => 'transportManager__select'
            )
        );

        $this->add($transportManager);

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
