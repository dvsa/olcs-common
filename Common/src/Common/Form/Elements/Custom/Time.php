<?php

/**
 * Time
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\Custom;

use Laminas\Form\Element as ZendElement;

/**
 * Time
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Time extends ZendElement\Time
{
    /**
     * Should return an array specification compatible with
     * {@link Laminas\InputFilter\Factory::createInput()}.
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
