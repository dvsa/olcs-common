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
        $postcodeSearch->setAttribute('class', 'short');
        $postcodeSearch->setAttribute('data-container-class', 'inline');
        $this->add($postcodeSearch);

        $searchButton = new Button('search', array('label' => 'Find address'));
        $searchButton->setAttribute('class', 'action--secondary large');
        $searchButton->setAttribute('data-container-class', 'inline');

        $this->add($searchButton);
    }
}
