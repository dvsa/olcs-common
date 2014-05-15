<?php
namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;

class Time extends ZendElement\Time
{
    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => false,
        );
    }
}
