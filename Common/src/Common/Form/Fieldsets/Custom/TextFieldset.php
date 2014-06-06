<?php

/**
 * TextFieldset
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Fieldsets\Custom;

use Zend\Form\Fieldset;

/**
 * TextFieldset
 *
 * @author Someone <someone@valtech.co.uk>
 */
class TextFieldset extends Fieldset
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setOptions(array('wrapElements', false));

        $this->add(
            array(
                'name' => 'text',
                'options' => array(
                    //'label' => 'Name of the brand'
                ),
            )
        );
    }

    public function getInputSpecification()
    {
        return array();
    }
}
