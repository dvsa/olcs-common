<?php

namespace Common\Form\Fieldsets\Custom;

use Zend\Form\Fieldset;

class TextFieldset extends Fieldset
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);


        $this->add(array(
            'name' => 'text',
            'options' => array(
                //'label' => 'Name of the brand'
            ),
        ));
    }

    public function getInputSpecification()
    {
        return array();
    }

}