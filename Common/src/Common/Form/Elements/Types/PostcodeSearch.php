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
    /** @var int Count the number of instances of this class */
    private static $count = 0;

    /**
     * Setup the elements
     *
     * @param null|int|string $name    Optional name for the element
     * @param array           $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        self::$count++;

        $postcodeSearch = new Text('postcode');
        $postcodeSearch->setAttributes(
            array(
                'class' => 'short js-input',
                'data-container-class' => 'inline',
                'id' => 'postcodeInput'. self::$count,
            )
        );
        $postcodeSearch->setOption('remove_if_readonly', true);

        $this->add($postcodeSearch);

        $searchButton = new Button('search', array('label' => 'Find address'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary js-find',
                'data-container-class' => 'inline'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);

        $selectAddress = new Select('addresses', array('label' => '', 'empty_option' => 'Please select'));
        $selectAddress->setAttributes(
            array(
                'data-container-class' => 'compound address__select'
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
        $manualLink->setValue(
            '<p class="visually-hidden hint postcode-connectionLost">%s</p><p class="hint"><a href=#>%s</a></p>'
        );
        $manualLink->setTokens(['postcode.error.not-available', 'postcode.address.manual_entry']);
        $manualLink->setAttributes(
            array(
                'data-container-class' => 'js-visible'
            )
        );
        $manualLink->setOption('remove_if_readonly', true);

        $this->add($manualLink);
    }

    /**
     * Set messages
     * NB Not sure if this is used
     *
     * @param mixed $messages Messages
     *
     * @return void
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * Get messages
     * NB Not sure if this is used
     *
     * @return array
     */
    public function getMessages($elementName = null)
    {
        return $this->messages;
    }
}
