<?php

/**
 * PostcodeSearch fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Element;
use Zend\Form\Element\Text;
use Zend\Form\Element\Button;

/**
 * PostcodeSearch fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PostcodeSearch extends Element
{
    /**
     * Holds the postcode element
     *
     * @var Text
     */
    protected $postcodeElement;

    /**
     * Holds the search button
     *
     * @var Button
     */
    protected $searchButton;

    /**
     * Setup the elements
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->postcodeElement = new Text('postcode', array('label' => ''));
        $this->postcodeElement->setAttribute('class', 'short');

        $this->searchButton = new Button('search', array('label' => 'Find address'));
        $this->searchButton->setAttribute('class', 'action--primary');
    }

    /**
     * Getter Postcode Element
     *
     * @return \Zend\Form\Element\Text
     */
    public function getPostcodeElement()
    {
        return $this->postcodeElement;
    }

    /**
     * Getter for search button
     *
     * @return Button
     */
    public function getSearchButton()
    {
        return $this->searchButton;
    }
}
