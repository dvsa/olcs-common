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
use Common\Form\Elements\Types\HtmlTranslated;

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
                'class' => 'short js-input',
                'data-container-class' => 'inline'
            )
        );

        $this->add($postcodeSearch);

        $searchButton = new Button('search', array('label' => 'Find address'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary large js-find',
                'data-container-class' => 'inline'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);

        $selectAddress = new Select('addresses', array('label' => '', 'empty_option' => 'Please select'));
        $selectAddress->setAttributes(
            array(
                'data-container-class' => 'address__select'
            )
        );

        $this->add($selectAddress);

        $selectButton = new Button('select', array('label' => 'Select'));
        $selectButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary js-select',
                'data-container-class' => 'js-hidden'
            )
        );
        $selectButton->setValue('select');

        $this->add($selectButton);

        $manualLink = new HtmlTranslated('manual-link');
        $manualLink->setValue('<p class="hint--small"><a href=#>%s</a></p>');
        $manualLink->setTokens(['postcode.address.manual_entry' ]);
        $manualLink->setAttributes(
            array(
                'data-container-class' => 'js-visible'
            )
        );

        $this->add($manualLink);
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
